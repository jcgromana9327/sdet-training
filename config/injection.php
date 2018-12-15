<?php

return [
    "cpo-code-injection" => "x || ping -n 5 127.0.0.1",
    "ca.apw-code-injection" => "|| sleep 5",
    "ca.apw-sql-injection" => 'AND SLEEP(3)=0',
    "ca.cp-code-injection" => "; x || sleep 3",
    "ca.cp-code-injection-1" => "|| sleep 3",
    "ca.cp-sql-injection" => " AND SLEEP(3)=0",




];
