<?php
// config/mail.php
return [
  'from_email' => 'no-reply@nexgizmo.test',
  'from_name'  => 'NexGizmo',
  'host'       => 'smtp.mailtrap.io',   // change to your smtp host
  'username'   => 'your_smtp_user',
  'password'   => 'your_smtp_pass',
  'port'       => 587,
  'encryption' => 'tls',
  'admin_notify_email' => 'owner@example.com',
];
