<?php

namespace AppBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Util\AdminMenusIcon;
use AppBundle\Util\AdminMenusInit;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Form\UserAdminType;
use AppBundle\Form\ProfileType;
use AppBundle\Entity\User;
use AppBundle\Util\Helpers;
use AppBundle\Form\NewPasswordType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserController extends Controller {
    public function usersAction(Request $request) {
        $request->getSession()->set('left_menu', 'users');
        return $this->render('@App/Admin/user/users.html.twig');
    }

    public function getAjaxUsersAction(Request $request) {
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search');
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('AppBundle:User')->getUsers($start, $length, $search['value']);
        $recordsFiltered = count($em->getRepository('AppBundle:User')->getUsers(0, 'all', $search['value']));
        $recordsTotal = $em->getRepository('AppBundle:User')->getCount();
        $count = count($users);
        $adminMenus = $this->get('admin.menus');
        $translator = $this->get('translator');

        for ($i = 0; $i < $count; $i++) {
            $menuInit = new AdminMenusInit($translator->trans('options', array(), 'admin'), $users[$i]['id']);
            $menuInit->addMenu('Edit', AdminMenusIcon::EDIT, 'admin_user_edit', array('username' => $users[$i]['username']));
            $menuInit
                ->addMenu('Details', AdminMenusIcon::SEARCH, null)
                ->addDivider()
                ->addMenu('Delete', AdminMenusIcon::TRASH, 'admin_user_delete', array('username' => $users[$i]['username']));
            $links = $adminMenus->buildListMenu($menuInit);
            $users[$i]['actions'] = $links;
        }
        $info = array();
        $info['draw'] = (int) $request->get('draw');
        $info['recordsTotal'] = (int) $recordsTotal;
        $info['recordsFiltered'] = (int) $recordsFiltered;
        $info['data'] = $users;

        return new \Symfony\Component\HttpFoundation\JsonResponse($info);
    }

    public function getAjaxUserAction(Request $request) {
        $id = $request->get('id', false);
        $translator = $this->get('translator');
        if (!$id)
            return new Response($translator->trans('paramter.error', array(), 'admin'));

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->getUserById($id);

        if (count($user) == 0)
            return new Response($translator->trans('info.nofound', array(), 'admin'), 200, array(
                'Content-Type' => 'text/html'
            ));

        return $this->render('@App/Admin/user/userPreview.html.twig', array(
            'user' => $user,
        ));
    }

    public function addUserAction(Request $request)
    {
        $form = new UserAdminType();
        $user = new User();
        $form = $this->createForm($form, $user);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();
            if ($form->isValid()) {
                $password = $user->getPassword();
                $user->setEnabled(true);
                $user->setLocked(false);
                $user->setRol('ROLE_USER');
                $user->setSalt(md5(uniqid($user->getUsername())));
                $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                $passwordEncoder = $encoder->encodePassword(
                    $user->getPassword(), $user->getSalt()
                );
                $user->setPassword($passwordEncoder);
                $em->persist($user);

                $name = $user->getUsername();
                $em->flush();

                $message = Helpers::messageWelcomeUser($this->container, $user, $password);
                $this->container->get('mailer')->send($message);

                $this->get('session')->getFlashBag()->add('info', 'User <b>' . $name . '</b> has been added.');
                return $this->redirect($this->generateUrl('admin_users'));


            }
        }
        return $this->render('@App/Admin/user/form.html.twig', array(
            'form' => $form->createView(),
            'action' => 'New'
        ));
    }

    public function editUserAction(Request $request, $username) {
        $request->getSession()->set('left_menu', 'users');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneByUsername($username);
        if (!$user)
            return Helpers::error404($this, 'User not found', 'Back to list users', 'fa-users', $this->generateUrl('admin_users'));

        $passwordOld = $user->getPassword();

        $userType = new UserAdminType();
        $form = $this->createForm($userType, $user);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            $em = $this->getDoctrine()->getManager();
            if ($form->isValid()) {
                if ($user->getPassword() == null)
                    $user->setPassword($passwordOld);
                else {
                    $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                    $passwordEncoder = $encoder->encodePassword(
                        $user->getPassword(), $user->getSalt()
                    );
                    $user->setPassword($passwordEncoder);
                }
                $em->persist($user);
                $name = $user->getUsername();
                $em->flush();
                $this->get('session')->getFlashBag()->add('info', 'User <b>'.$name.'</b> has been edited.');
                return $this->redirect($this->generateUrl('admin_users'));
            }
        }

        return $this->render('@App/Admin/user/form.html.twig', array(
            'form' => $form->createView(),
            'action' => 'Edit',
            'username' => $username
        ));
    }

    public function deleteUserAction(Request $request, $username)
    {
        $translator = $this->get('translator');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->findOneByUsername($username);

        if (!$user)
            return Helpers::error404($this, 'User not found', 'Back to list users', 'fa-users', $this->generateUrl('admin_users'));


        $username = $user->getUsername();
        $em->remove($user);
        $em->flush();

        $this->get('session')->getFlashBag()->add('info', 'User <b>'.$username.'</b> has been deleted.');
        return $this->redirect($this->generateUrl('admin_users'));
    }

    public function loginAction(Request $request)
    {
        $helper = $this->get('security.authentication_utils');

        return $this->render('@App/Admin/login.html.twig', array(
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError()
        ));
    }

    public function editProfileAction(Request $request, $username) {
        $em = $this->getDoctrine()->getManager();
        $userLogger = $this->getUser();
        $user = $em->getRepository('AppBundle:User')->findOneByUsername($username);
        $error = false;

        if($userLogger->getUsername() != $user->getUsername())
            return Helpers::error404($this, 'You do not have permission to edit this profile', 'Back to dashboard', 'fa-users', $this->generateUrl('dashboard'));
        if (!$user)
            return Helpers::error404($this, 'User not found', 'Back to list users', 'fa-users', $this->generateUrl('admin_users'));

        $passwordOld = $user->getPassword();

        $userType = new ProfileType();
        $form = $this->createForm($userType, $user);

        if ($request->getMethod() == 'POST') {
            $data_post = $request->get('appbundle_user');
            if($data_post['checkPassword']){
                $old_pwd = $data_post['currect_password'];
                //$new_pwd = $request->get('new_password');
                $userAut = $this->getUser();
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($userAut);
                $old_pwd_encoded = $encoder->encodePassword($old_pwd, $userAut->getSalt());

                if($userAut->getPassword() != $old_pwd_encoded){
                    $this->get('session')->getFlashBag()->set('error_password_msg', "Wrong old password");
                    $error = true;
                }
                else{
                    $form->handleRequest($request);
                    $em = $this->getDoctrine()->getManager();
                    if ($user->getPassword() == null)
                        $user->setPassword($passwordOld);
                    else {
                        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                        $passwordEncoder = $encoder->encodePassword(
                            $user->getPassword(), $user->getSalt()
                        );
                        $user->setPassword($passwordEncoder);
                    }
                    $em->persist($user);
                    $name = $user->getUsername();
                    $em->flush();
                    $this->get('session')->getFlashBag()->add('info', 'User <b>'.$name.'</b> has been edited.');
                    return $this->redirect($this->generateUrl('dashboard'));
                }
            }
            else{
                $form->handleRequest($request);
                $em = $this->getDoctrine()->getManager();
                if ($user->getPassword() == null)
                    $user->setPassword($passwordOld);
                else {
                    $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                    $passwordEncoder = $encoder->encodePassword(
                        $user->getPassword(), $user->getSalt()
                    );
                    $user->setPassword($passwordEncoder);
                }
                $em->persist($user);
                $name = $user->getUsername();
                $em->flush();
                $this->get('session')->getFlashBag()->add('info', 'User <b>'.$name.'</b> has been edited.');
                return $this->redirect($this->generateUrl('dashboard'));
            }
        }
        $dataChange = array();
        if($error){
            if($data_post['changeData'] != ''){
                $list_input = explode(',', $data_post['changeData']);
                for($i=0; $i<count($list_input); $i++){
                    $input_data = explode(':',$list_input[$i]) ;
                    $temp = array();
                    $temp['id'] = $input_data[0];
                    $temp['value'] = $input_data[1];
                    $dataChange[] = $temp;
                }
            }
        }

        return $this->render('@App/Admin/user/profile.html.twig', array(
            'form' => $form->createView(),
            'username' => $username,
            'error' => $error,
            'data_change' => $dataChange
        ));
    }

    public function forgetPasswordAction(Request $request)
    {
        if ($request->getMethod() == 'POST') {
            $step = $request->get('step', false);
            if ($step) {
                $em = $this->getDoctrine()->getManager();
                if ($step == 'first') {
                    $email = $request->get('email', false);
                    $user = $em->getRepository('AppBundle:User')->checkEmailForgetPswd($email, \Doctrine\ORM\Query::HYDRATE_OBJECT);
                    if ($user) {
                        $temp = md5(uniqid($email));
                        $count = strlen($temp);
                        $code = '';
                        for ($i = 0; $i < 6; $i++)
                            $code .= $temp[mt_rand(0, $count - 1)];

                        $code = strtoupper($code);

                        //If the code matches another user delete the previous
                        $userWithSomeCode = $em->getRepository('AppBundle:User')->findOneBy(array('recovery_code' => $code));
                        if ($userWithSomeCode) {
                            $userWithSomeCode->setRecoveryCode(null);
                            $em->persist($userWithSomeCode);
                            $em->flush();
                        }

                        $user->setRecoveryCode($code);
                        $em->persist($user);
                        $em->flush();

                        $container = $this->container;
                        $bodyHTML = $container->get('twig')->render('AppBundle:Admin/emails:forget_password.html.twig', array(
                            'code' => $code,
                            'name' => $user->getName()
                        ));
                        $bodyText = $container->get('twig')->render('AppBundle:Admin/emails:forget_password.txt.twig', array(
                            'code' => $code,
                            'name' => $user->getName()
                        ));

                        $translator = $this->get('translator');

                        $message = Helpers::buildMessageEmail('One-time passcode from Taskit', $email, $bodyHTML, $bodyText);
                        $container->get('mailer')->send($message);

                        return $this->render('AppBundle:Admin/user:forget_password_enter_code.html.twig', array(
                            'email' => $email,
                        ));
                    }
                }
                elseif ($step == 'second') {
                    $email = $request->get('email');
                    $code = strtoupper($request->get('code', false));
                    if ($code && $code != null && $code != '') {
                        $user = $em->getRepository('AppBundle:User')->checkEmailForgetPswd($email, \Doctrine\ORM\Query::HYDRATE_OBJECT);
                        if ($user && $user->getRecoveryCode() == $request->get('code')) {
                            //login the user
                            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                            $this->container->get('security.token_storage')->setToken($token);
                            $request->getSession()->set('forget_password_email', $email);
                            $request->getSession()->set('forget_password_code', $code);
                            return $this->redirect($this->generateUrl('forget_password_new_password'));
                        }
                    }
                    return $this->render('AppBundle:Admin/user:forget_password_enter_code.html.twig', array(
                        'email' => $email,
                    ));

                }
            }
        }

        return $this->render('AppBundle:Admin/user:forgetPassword.html.twig');
    }

    public function emailCheckForgetPswdAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $email = $em->getRepository('AppBundle:User')->checkEmailForgetPswd($request->get('email'));
        if ($email)
            return new JsonResponse(true);
        return new JsonResponse(false);
    }

    public function codeCheckForgetPswdAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->checkEmailForgetPswd($request->get('email'));
        if ($user['recovery_code'] == $request->get('code'))
            return new JsonResponse(true);
        return new JsonResponse(false);
    }

    public function forgetPasswordNewPasswordAction(Request $request)
    {
        $email = $request->getSession()->get('forget_password_email', false);
        $code = $request->getSession()->get('forget_password_code', false);
        if ($email && $code) {
            $user = $this->getUser();
            $form = $this->createForm(new NewPasswordType(), $user);
            if ($request->getMethod() == 'POST') {
                //fix problem file not found
                //end fix problem
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                    $new_password = $encoder->encodePassword(
                        $user->getPassword(), $user->getSalt()
                    );
                    $user->setPassword($new_password);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    return $this->redirect($this->generateUrl('dashboard'));
                }
            }
            return $this->render('AppBundle:Admin/user:forget_password_new_password.html.twig', array(
                'form' => $form->createView(),
            ));
        } else
            return $this->redirect($this->generateUrl('forget_password'));
    }

}

