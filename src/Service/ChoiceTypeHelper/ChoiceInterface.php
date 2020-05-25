<?php


namespace App\Service\ChoiceTypeHelper;


interface ChoiceInterface
{
    public function getChoiceLabel() : ?string;
    public function getChoiceValue() : ?string;
}