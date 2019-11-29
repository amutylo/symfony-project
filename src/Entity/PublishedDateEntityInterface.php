<?php


namespace App\Entity;

interface PublishedDateEntityInterface {
    public function setPublished(\DateTimeInterface $publisged): PublishedDateEntityInterface;
}