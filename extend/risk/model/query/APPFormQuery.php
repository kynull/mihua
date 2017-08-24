<?php
namespace risk\model\query;

use risk\model\AppForm;

class APPFormQuery extends AppForm
{

    public $id_workflow;

    public $advice;

    //对外决策原因
    public $reason = array();
}