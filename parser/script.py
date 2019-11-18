#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import time
import config
import funcs_class
import mail

# directory = '/home/den/PycharmProjects/priceScript/file/'
archive_formats = config.archive_formats
file_formats = config.file_formats
directory = config.directory


def script(setting=None, log=None):
    start = time.time()
    if setting is None:
        return False

    # удаление всех файлов в директории
    if os.listdir(directory):
        for xx in os.listdir(directory):
            os.remove(directory + xx)

    filename = mail.get_attached_for_email(log, setting['email'])
    files = os.listdir(directory)
    print filename
    print files
    if len(files) == 0:
        log.warning('Not files in directory')
        raise False
    elif len(files) > 1:
        log.warning('More than 1 file in directory')
    else:
        log.info('Attachment is found')

    # проверка формата файла
    format_file = files[0].split('.')[-1].strip().lower()
    name_file = files[0]
    if filename == name_file:
        log.info("It is the correct file")

    # разархивация и удаление архива
    if format_file in archive_formats.keys():
        log.info('File is ARCHIVE')
        func_unarchived = getattr(funcs_class, archive_formats[format_file])
        func_unarchived(directory, name_file, log)
        os.remove(directory + name_file)
        # определение новых параметров файла
        files = os.listdir(directory)
        format_file = files[0].split('.')[-1]
        name_file = files[0]
    elif format_file in file_formats:
        log.info('File is data Price')

    # обработка таблицы данных
    res = getattr(funcs_class.Funcs(directory, name_file, setting, log), (file_formats[format_file.lower()]))()
    log.info('working times: %d' % (time.time() - start))
    return res


if __name__ == '__main__':
    script()
