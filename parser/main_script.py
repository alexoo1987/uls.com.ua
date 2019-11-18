#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
from datetime import datetime as dt
import mysql
import script
import time
from Logger import Log

__author__ = 'i_delight'

log = Log('log/bot.log')
log.debug("********Start********")


def main_script():
    my_db, con = mysql.my_db(log)

    q = '''select * from import_setting'''

    my_db.execute(q)
    result = my_db.fetchall()
    all_suppliers = []

    now = [dt.now().hour, dt.now().minute]

    assert isinstance(result, tuple)
    for row in result:
        supplier_id = int(row[1])
        setting = eval(row[2])
        setting['supplier_id'] = supplier_id
        start_days = setting['start']['dayOfWeek']
        start_time = setting['start']['time']

        #     проверка на совпадение времени
        if (str(dt.today().isoweekday()) in start_days.split(',')) and (str(now[0]) in start_time.split(',')):
        	all_suppliers.append(setting)

        #if supplier_id in [142]:
         #   all_suppliers.append(setting)

    print len(all_suppliers)

    print len(all_suppliers)

    if len(all_suppliers):
        query = []
        for sett in all_suppliers:
            flag = True
            n = 0
            while flag:
                try:
                    print sett
                    log.debug('##### Start for: supplier_id = %s #####' % sett['supplier_id'])
                    script.script(sett, log)
                    log.debug('##### Finished for: supplier_id = %s #####' % sett['supplier_id'])
                    q = """UPDATE import_setting
                           SET log = 'OK, %s', status = 1, last_date = NOW()
                           WHERE supplier_id = %d""" % (dt.now(), sett['supplier_id'])
                    query.append(q)
                    qq = """ UPDATE suppliers
                            SET update_time = NOW()
                            WHERE id = %d""" % sett['supplier_id']
                    query.append(qq)
                    flag = False
                except BaseException as e:
                    log.warning('WARNING -> "%s", for: supplier_id = %d' % (e, sett['supplier_id']))
                    q = """UPDATE import_setting
                           SET log = "%s", status = 0
                           WHERE supplier_id = %d""" % (e, sett['supplier_id'])
                    print e
                    if 'Broken pipe' in e:
                        if n > 4:
                            flag = False
                        flag = True
                        n += 1
                        continue
                finally:
                    try:
                        if len(query) == 2:
                            for i in query:
                                my_db.execute(i)
                        else:
                            my_db.execute(q)
                        con.commit()
                        log.debug('updated log import_setting')
                        flag == False
                        break
                    except BaseException as e:
                        log.debug('error in updated log import_setting')
                        con.rollback()
                        flag == False
                        break
    con.close()

if __name__ == '__main__':
    main_script()
    log.debug("********Stop********")
    sys.exit(0)
