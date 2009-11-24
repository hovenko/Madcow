<?php

interface DF_Web_User {
    public function validate_credentials();
    public function findByUid($uid);
    public function getUid();
}
