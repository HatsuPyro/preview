<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.11.2018
 * Time: 15:07
 */

namespace Pyrobyte\Sesame\Action;


use Pyrobyte\Sesame\ActionAbstract;

class GetGroups extends ActionAbstract
{
    protected $url = 'groups_list';
    protected $resultClass = \Pyrobyte\Sesame\Result\GetGroups::class;

}