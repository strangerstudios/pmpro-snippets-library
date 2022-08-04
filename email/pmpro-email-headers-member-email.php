/*
Bcc admin on PMPro member only emails
You can change the conditional to check for a certain $email->template or some other condition before adding the BCC.

 * title: BCC the Admin on Member Emails
 * layout: snippet
 * collection: email
 * category: bcc
*/

function my_pmpro_email_headers($headers, $email)
{
	//bcc emails not already going to admin_email
        if($email->email != get_bloginfo("admin_email"))
	{
		//add bcc
		$headers[] = "Bcc:" . get_bloginfo("admin_email");
	}

	return $headers;
}
add_filter("pmpro_email_headers", "my_pmpro_email_headers", 10, 2);
