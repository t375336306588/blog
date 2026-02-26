<?php

namespace App\Page\Creator;

interface ICreatorDB {

    public function setArticleCreator(ICreator $creator);

    public function setCategoryCreator(ICreator $creator);

}