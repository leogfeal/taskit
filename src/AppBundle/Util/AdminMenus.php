<?php

/**
 * Description of AdminMenus
 *
 * @author Daymer Rodriguez Fillad and Nelson Martinez Romero
 */

namespace AppBundle\Util;

use \Symfony\Component\Routing\Router;

class AdminMenus {

    private $router;

    public function __construct(Router $router) {
        $this->router = $router;
    }

    /**
     * Build a menu to manage the object.
     *
     * @param  title btn
     * @param  array $links options
     * @return html
     */
    public function buildListMenu(AdminMenusInit $menusInit) {
        $options = $menusInit->getOptions();
        $links = '';
        foreach ($options as $opt) {
            if ($opt != 'divider') {
                $url = '#';
                if (isset($opt['modal']))
                        $links .= '<li><a href="#" class='+$opt['class']+'><i class="fa ' . $opt['icon'] . ' fa-fw action-link"></i> ' . $opt['title'] . '</a></li>';
                elseif (isset($opt['disabled']))
                    $links .= '<li class="disabled"><a href="#"><i class="fa ' . $opt['icon'] . ' fa-fw action-link"></i> ' . $opt['title'] . '</a></li>';
                else {
                    if ($opt['target'] != null)
                        $opt['target'] = $opt['target'] ? $opt['target'] : "";
                    if ($opt['route'] != null)
                        $url = $opt['routeParameters'] ? $this->router->generate($opt['route'], $opt['routeParameters']) : $this->router->generate($opt['route']);
                    if ($opt['icon'] == AdminMenusIcon::TRASH)
                        $links .= '<li><a href="#" onclick="delObject(\'' . $url . '\', ' . $menusInit->getId() . ')" data-toggle="modal" data-target="#modalView"><i class="fa ' . $opt['icon'] . ' fa-fw action-link-delete"></i> ' . $opt['title'] . '</a></li>';
                    elseif ($opt['icon'] == AdminMenusIcon::SEARCH)
                        $links .= '<li><a href="#" onclick="viewObject(' . $menusInit->getId() . ')" data-toggle="modal" data-target="#modalView"><i class="fa ' . $opt['icon'] . ' fa-fw action-link"></i> ' . $opt['title'] . '</a></li>';
                    else
                        $links .= '<li><a href="' . $url . '" target = "'.$opt['target'].'"><i class="fa ' . $opt['icon'] . ' fa-fw action-link"></i> ' . $opt['title'] . '</a></li>';
                }
            } else
                $links.= '<li class="divider margin-none"></li>';
        }
        $html = <<<EOH
<div class="btn-group">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        {$menusInit->getTitle()} <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
      $links
    </ul>
</div>                
EOH;

        return $html;
    }
	//revizar la options Disable que como esta no functiona
	public function buildMenuInline(AdminMenusInit $menusInit) {
        $options = $menusInit->getOptions();
        $links = '';
        foreach ($options as $opt) {
            if ($opt != 'divider') {
                $url = '#';
                if (isset($opt['modal']))
                        $links .= '<a href="#" class="btn btn-sm btn-default '.$opt['class'].'" title="'.$opt['title'].'"><i class="fa ' . $opt['icon'] . ' fa-fw action-link"></i> </a>';
                elseif (isset($opt['disabled']))
                    $links .= '<a href="#" title="'.$opt['title'].'" class="disabled btn btn-sm btn-default"><i class="fa ' . $opt['icon'] . ' fa-fw action-link"></i></a>';
                else {
                    if ($opt['target'] != null)
                        $opt['target'] = $opt['target'] ? $opt['target'] : "";
                    if ($opt['route'] != null)
                        $url = $opt['routeParameters'] ? $this->router->generate($opt['route'], $opt['routeParameters']) : $this->router->generate($opt['route']);
                    if ($opt['icon'] == AdminMenusIcon::TRASH)
                        $links .= '<a href="#" title="'.$opt['title'].'" class="btn btn-sm btn-default" onclick="delObject(\'' . $url . '\', ' . $menusInit->getId() . ')" data-toggle="modal" data-target="#modalView"><i class="fa ' . $opt['icon'] . ' fa-fw action-link-delete"></i></a>';
                    elseif ($opt['icon'] == AdminMenusIcon::SEARCH)
                        $links .= '<a href="#" title="'.$opt['title'].'" class="btn btn-sm btn-default" onclick="viewObject(' . $menusInit->getId() . ')" data-toggle="modal" data-target="#modalView"><i class="fa ' . $opt['icon'] . ' fa-fw action-link"></i></a>';
                    else
                        $links .= '<a href="'.$url.'" class="btn btn-sm btn-default" title="'.$opt['title'].'" target = "'.$opt['target'].'"><i class="fa ' . $opt['icon'] . ' fa-fw action-link"></i></a>';
                }
            }
        }
        $html = <<<EOH
		<div class="form-group" style="display: inline-flex;">
			$links
		</div>
EOH;
        return $html;
    }

}

?>
