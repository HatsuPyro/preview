<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.09.2018
 * Time: 11:06
 */

namespace Pyrobyte\Sesame\Action;


use Pyrobyte\Sesame\ActionAbstract;

class SimList extends ActionAbstract
{
    protected $url = 'get_sim_list';
    protected $method = 'GET';
    protected $resultClass = \Pyrobyte\Sesame\Result\SimList::class;

    public function __construct($group = null)
    {
        if($group) {
            $this->routeParams['group'] = $group;
        }
    }

}