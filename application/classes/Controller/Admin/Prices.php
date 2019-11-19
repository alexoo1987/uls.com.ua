<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Prices extends Controller_Admin_Application
{

    public $hostname = '{imap.ukr.net:143}INBOX';
    public $username = 'eparts.kiev.ua@ukr.net';
    public $password = '950667817282d';

    public $archives = array(
        'rar' => 'unrar e ',
        'zip' => 'unzip ',
    );

    public function action_run()
    {

        $uploads = ORM::factory('PricesSetting')->find_all()->as_array();

        foreach ($uploads AS $upload) {
            $upload->clear_log();

            $file = false;

            if ($upload->type == 1){
                $file = $this->get_email_file($upload);
            }

            if (!$file) $upload->log('Ссылка на файл пустая');

            $this->import($upload, $file);
            exit();
        }
    }


    public function get_email_file($setting)
    {
        $supplier_dir = "uploads/prices/" . $setting->Supplier->name;
        /* try to connect */
        $inbox = imap_open($this->hostname, $this->username, $this->password) or die('Cannot connect to mail server: ' . imap_last_error());

        if (!$inbox) $setting->log('Не могу подключится к почте');

        $emails = imap_search($inbox, 'FROM "' . $setting->email_from . '" SUBJECT "' . $setting->email_subject . '"');

        /* useful only if the above search is set to 'ALL' */
        $max_emails = 16;

        /* if any emails found, iterate through each email */
        if ($emails) {

            $count = 1;

            /* put the newest emails on top */
            rsort($emails);

            /* for every email... */
            foreach ($emails as $email_number) {

                /* get information specific to this email */
                $overview = imap_fetch_overview($inbox, $email_number, 0);

                /* get mail message, not actually used here.
                   Refer to http://php.net/manual/en/function.imap-fetchbody.php
                   for details on the third parameter.
                 */
                $message = imap_fetchbody($inbox, $email_number, 2);

                /* get mail structure */
                $structure = imap_fetchstructure($inbox, $email_number);

                $attachments = array();

                /* if any attachments found... */
                if (isset($structure->parts) && count($structure->parts)) {
                    for ($i = 0; $i < count($structure->parts); $i++) {
                        $attachments[$i] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );

                        if ($structure->parts[$i]->ifdparameters) {
                            foreach ($structure->parts[$i]->dparameters as $object) {
                                if (strtolower($object->attribute) == 'filename') {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename'] = $object->value;
                                }
                            }
                        }

                        if ($structure->parts[$i]->ifparameters) {
                            foreach ($structure->parts[$i]->parameters as $object) {
                                if (strtolower($object->attribute) == 'name') {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name'] = $object->value;
                                }
                            }
                        }

                        if ($attachments[$i]['is_attachment']) {
                            $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i + 1);

                            /* 3 = BASE64 encoding */
                            if ($structure->parts[$i]->encoding == 3) {
                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                            } /* 4 = QUOTED-PRINTABLE encoding */
                            elseif ($structure->parts[$i]->encoding == 4) {
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                            }
                        }
                    }
                }

                /* iterate through each attachment and save it */
                if (is_dir($supplier_dir)) {

                    $files = glob($supplier_dir . "/*"); // get all file names
                    foreach ($files as $file) { // iterate files
                        if (is_file($file))
                            unlink($file); // delete file
                    }

                } else {
                    mkdir($supplier_dir);
                }

                foreach ($attachments as $attachment) {
                    if ($attachment['is_attachment'] == 1) {
                        $filename = $attachment['name'];
                        if (empty($filename)) $filename = $attachment['filename'];

                        if (empty($filename)) $filename = time() . ".dat";

                        /* prefix the email number to the filename in case two emails
                         * have the attachment with the same file name.
                         */
                        $file_path = "uploads/prices/" . $setting->Supplier->name . "/" . $filename;
                        $fp = fopen($file_path, "w+");
                        fwrite($fp, $attachment['attachment']);
                        $setting->log('Файл успешно скачан');
                        fclose($fp);
                    }
                }

                if ($count++ >= $max_emails) break;
            }

        } else {
            $setting->log('Нет писем с указаными email и темой');
        }

        /* close the connection */
        imap_close($inbox);
        chdir($supplier_dir);

        if ($setting->archive) {
            exec($this->archives[$setting->archive] . $filename);
            $setting->log('Архив успешно расспакован');
        }

        exec("mv *." . $setting->file_format . " price." . $setting->file_format);

        return "price." . $setting->file_format;
    }

    public function import($setting, $filename)
    {
//        chdir('/var/www/html/eparts/uploads/prices/ELIT');

//        PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
        $spreadsheet = Spreadsheet::factory(
            array(
                'filename' => $filename
            ));
//            ->load();
//            ->read();
        $spreadsheet->load();
        var_dump($spreadsheet);exit();
        foreach ($spreadsheet as $v) {
            echo $v['A'] . ',';
            exit();
        }
        exit();
    }

}

