# -*- coding: utf-8 -*-

import config
import quopri
import base64
from imaplib import IMAP4, IMAP4_SSL
import imaplib
imaplib._MAXLINE = 40000
import codecs
import email
import os
import locale
CHARSET = locale.getpreferredencoding(do_setlocale=False)
del locale


detach_dir = '.'  # directory where to save attachments (default: current)
username = config.gmail_username
password = config.gmail_password

# connecting to the gmail imap server
# server = IMAP4_SSL('imap.gmail.com')
# connecting to the UKR imap server
server = IMAP4('imap.ukr.net')
r, d = server.login(username, password)
filename = 'not file'
flag1 = True


def get_attached_for_email(log, params):
    global r, d, flag1, server

    while flag1:
        print r, d
        if 'OK' in r and 'LOGIN completed' in d:
            server.select()
            flag1 = False
            break
        else:
            flag1 = True
            server = IMAP4('imap.ukr.net')
            r, d = server.login(username, password)
            continue
    subject = params['subject']
    sender = params['from']
    ext = params['ext']
    log.info('From: %s, Sub: %s' % (sender, subject))

    log.debug('Start parsing email')
    flag = False
    global filename
    # проверка писем по заданному отправителю и теме items = id, flg = OK, BAD
    # flg, items = server.search(None, '(UNSEEN FROM "%s" SUBJECT "%s")' % (sender, subject))
    flg, items = server.search(None, '(UNSEEN SUBJECT "%s")' % subject)
    items = items[0].split()  # getting the mails id
    # print items
    if len(items) > 0:
        flag = True
    else:
        log.warning('letters not found From: %s' % sender)
    repeat = False
    while flag:

        email_id = int(items[-1])
        if repeat:
            for i in items[::-1]:
                print i
                email_id = int(i)
                resp, data = server.fetch(str(email_id), "(RFC822)")
                email_body = data[0][1]  # getting the mail content
                mail = email.message_from_string(email_body)
                if sender.lower() in mail['From'].lower():
                    break

        # достает тело письма, и "читает" его
        resp, data = server.fetch(str(email_id), "(RFC822)")
        email_body = data[0][1]  # getting the mail content
        mail = email.message_from_string(email_body)
        print "[" + mail["From"] + "]: " + mail["Subject"]
        if sender.lower() not in mail['From'].lower():
            repeat = True
            continue
        else:
            repeat = False

        # Check if any attachments at all
        if mail.get_content_maintype() != 'multipart':
            log.warning('content not multipart')
            break

        for part in mail.walk():
            if not part.get_filename() or 'xml' in part.get_filename():
                continue
            # print part.get_filename()
            if part.get_content_maintype() == 'image':
                continue
            if part.get_content_maintype() == 'multipart':
                continue

            # is this part an attachment ?
            # if not part.get('Content-Disposition'):
            #     continue

            filename = part.get_filename()
            counter = 1

            # if there is no filename, we create one with a counter to avoid duplicates
            if not filename:
                filename = 'part-%03d%s' % (counter, 'bin')
                counter += 1

            att_path = os.path.join('file', 'file.' + ext)

            if not os.path.isfile(att_path):
                log.debug('Save attachment')
                fp = open(att_path, 'wb')
                fp.write(part.get_payload(decode=True))
                fp.close()
                flag = False
                break
    return filename


if __name__ == '__main__':
    get_attached_for_email()
