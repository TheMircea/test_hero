<?php

namespace app\models;

interface ModelInterface
{

    public static function tableName(): string;

    public function save();

    public function load($post);

    public function delete();

    public static function findAll($conditions = [], $returnQuery = false);

    public static function findById($id, $class = '');
}
