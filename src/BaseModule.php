<?php
// src/BaseModule.php

abstract class BaseModule {
    protected $id;
    protected $name_en;
    protected $name_es;

    public function __construct($id, $en, $es) {
        $this->id = $id;
        $this->name_en = $en;
        $this->name_es = $es;
    }

    abstract public function render($lang = 'en');
    abstract public function handleAction($action, $data);

    public function getName($lang = 'en') {
        return $lang === 'es' ? $this->name_es : $this->name_en;
    }
}
