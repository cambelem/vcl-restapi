<?php
/*
  Licensed to the Apache Software Foundation (ASF) under one or more
  contributor license agreements.  See the NOTICE file distributed with
  this work for additional information regarding copyright ownership.
  The ASF licenses this file to You under the Apache License, Version 2.0
  (the "License"); you may not use this file except in compliance with
  the License.  You may obtain a copy of the License at

      http://www.apache.org/licenses/LICENSE-2.0

  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
*/
global $vclhost, $vcldb, $vclusername, $vclpassword, $cryptkey, $pemkey;
$vclhost = 'localhost'; # name of mysql server
$vcldb = 'vcl';         # name of mysql database
$vclusername = 'vcluser';      # username to access database
$vclpassword = 'password';      # password to access database

$cryptkey  = '0WR16QfZjBZjNz9xfc2w'; # generate with "openssl rand 32 | base64"

$pemkey = 'ihB9K44FNst8jR1HgB16'; # random passphrase - won't ever have to type it so make it long
?>
