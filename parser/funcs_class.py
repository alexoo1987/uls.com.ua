#!/usr/bin/env python
# -*- coding: utf-8 -*-
# from __future__ import unicode_literals
import pandas as pd
import zipfile
import rarfile
import time
import mysql
import numpy as np
import sys
import re

reload(sys)
sys.setdefaultencoding('utf8')


def func_zip(directory, name_file, log):
    log.info('Zip unarchived')
    with zipfile.ZipFile(directory + name_file, 'r') as zipArchive:
        log.info('%s' % zipArchive.namelist())
        zipArchive.extract(zipArchive.namelist()[0], directory)
        time.sleep(1)


def func_rar(directory, name_file, log):
    log.info('Rar unarchived')
    with rarfile.RarFile(directory + name_file, 'r') as rarArchive:
        log.info('%s' % rarArchive.namelist())
        rarArchive.extract(rarArchive.namelist()[0], directory)
        time.sleep(1)


class Funcs(object):
    def __init__(self, directory, name_file, setting, log):
        self._directory = directory
        self._name = name_file
        self._log = log
        self._frame = None

        self.my_db, self.con = mysql.my_db(self._log)

        self.variants = setting['columns']['variants']
        self.article_id = int(setting['columns']['article'])
        self.price_id = int(setting['columns']['price'])
        self.brand_id = int(setting['columns']['brand'])
        self.name_id = int(setting['columns']['name'])
        self.first_id = int(setting['firstLine'])
        self.supplier = int(setting['supplier_id'])
        self.currency = int(setting['currency_id'])
        self.encoding = int(setting['encoding'])
        self.operation_id = None
        self.count_id = None
        self.new_articles = None
        self.all_position = None
        self.is_csv = False
        self.all_result = None

    def func_params_from_mysql(self, brands, article):

        if self.func_add_operations():
            b = self.get_brands(brands)
            a = self.get_articles(article, b)

            if self.search_brands(list(set(brands) - set(b))):
                b = self.get_brands(brands)

            if self.search_article(list(set(article) - set(a.keys()))):
                if self.update_articles():
                    a = self.get_articles(article, b)

        else:
            return False

        self.func_to_delete_proposal()
        self.func_to_delete_unmatched()

        return b, a

    def func_add_operations(self, ):
        q = """ INSERT INTO operations (description, supplier_id)
                VALUES ('%s', '%d') """ % ('Обновление прайсов', self.supplier)

        try:
            self.my_db.execute(q)
            self.con.commit()
            self._log.info('update price')
        except BaseException as e:
            self._log.warning('error add operations %s' % e)
            self.con.rollback()
            return False

        qq = """SELECT MAX(a.id) FROM operations a
                WHERE a.supplier_id = '%d'""" % self.supplier

        self.my_db.execute(qq)
        self.operation_id = int(self.my_db.fetchall()[0][0])
        self._log.info('get operation id')

        return True

    def get_brands(self, brands):
        if len(brands) == 1:
            brands.append('')

        q = """ SELECT * from brands a"""

        self.my_db.execute(q)
        result = self.my_db.fetchall()
        res = [i.strip(' \t\n\r') for j in result for i in (j[1], j[2], j[3], j[4]) if i is not None]

        b = set(brands) & set(res)

        self._log.info('Get all brands in system: %s' % len(b))
        return list(b)

    def get_articles(self, article, b):
        if len(article) == 1:
            article.append('')

        step = 100000
        size = len(article) / step
        all_result = {}
        if size:

            for i in xrange(size):
                j = i + 1
                art = article[i * step:j * step]
                q = """SELECT a.article, a.id, a.brand, a.brand_long FROM parts a
                       WHERE a.article IN %s
                       AND a.brand IN %s""" % (str(tuple(art)), str(tuple(b)))

                self.my_db.execute(q)
                result = self.my_db.fetchall()

                for x in result:
                    if x[0] in all_result:
                        if type(all_result[x[0]]) != list:
                            all_result[x[0]] = [all_result[x[0]]]

                        all_result[x[0]].append({'id': x[1], 'brand': x[2].encode(), 'brand_long': x[3].encode()})
                    else:
                        all_result[x[0]] = {'id': x[1], 'brand': x[2].encode(), 'brand_long': x[3].encode()}

            q = """SELECT a.article, a.id, a.brand, a.brand_long FROM parts a
                       WHERE a.article IN %s
                       AND a.brand IN %s""" % (str(tuple(article[j * step:])), str(tuple(b)))

            self.my_db.execute(q)
            result = self.my_db.fetchall()

            for x in result:
                if x[0] in all_result:
                    if type(all_result[x[0]]) != list:
                        all_result[x[0]] = [all_result[x[0]]]

                    all_result[x[0]].append({'id': x[1], 'brand': x[2].encode(), 'brand_long': x[3].encode()})
                else:
                    all_result[x[0]] = {'id': x[1], 'brand': x[2].encode(), 'brand_long': x[3].encode()}
        else:
            q = """SELECT a.article, a.id, a.brand, a.brand_long FROM parts a
                       WHERE a.article IN %s
                       AND a.brand IN %s""" % (str(tuple(article)), str(tuple(b)))

            self.my_db.execute(q)
            result = self.my_db.fetchall()

            for x in result:
                if x[0] in all_result:
                    if type(all_result[x[0]]) != list:
                        all_result[x[0]] = [all_result[x[0]]]

                    all_result[x[0]].append({'id': x[1], 'brand': x[2], 'brand_long': x[3]})
                else:
                    all_result[x[0]] = {'id': x[1], 'brand': x[2], 'brand_long': x[3]}

        self._log.info('Get all articles in system: %s' % len(all_result.keys()))

        self.all_result = all_result

        return all_result

    def func_to_delete_proposal(self, ):
        q = """DELETE FROM priceitems WHERE supplier_id = '%d' """ % self.supplier

        try:
            self.my_db.execute(q)
            self.con.commit()
            self._log.debug('Delete proposal')
        except BaseException as e:
            self._log.warning('error in delete proposal: %s' % e)
            self.con.rollback()

        return True

    def func_to_delete_unmatched(self, ):
        q = """DELETE FROM unmatched WHERE supplier_id = '%d' """ % self.supplier

        try:
            self.my_db.execute(q)
            self.con.commit()
            self._log.debug('Delete unmatched')
        except BaseException as e:
            self._log.warning('error in delete unmatched: %s' % e)
            self.con.rollback()

        return True

    def func_bad_position(self, sets_brand, sets_article):
        self._log.debug('bad position')

        if sets_brand != '':

            sql_brand = """INSERT INTO unmatched
                           (brand, article, name, price, currency_id, amount, delivery, supplier_id, operation_id, reason)
                           VALUES %s """ % sets_brand
            try:
                self.my_db.execute(sql_brand)
                self.con.commit()
                self._log.debug('commit bad_brand')
            except BaseException as e:
                self._log.warning('error in bad_brand: %s' % e)
                self.con.rollback()

        if sets_article != '':

            sql_article = """ INSERT INTO unmatched
                        (brand, article, name, price, currency_id, amount, delivery, supplier_id, operation_id, reason)
                        VALUES %s """ % sets_article
            try:
                self.my_db.execute(sql_article)
                self.con.commit()
                self._log.debug('commit bad_article')
            except BaseException as er:
                self._log.warning('error in bad_article: %s' % er)
                self.con.rollback()

        return True

    def func_add_to_priceitems(self, setss):
        if type(setss) == list:
            print 'list'
            for i in setss:
                sets = i
                if sets == '':
                    return False

                sql = """ INSERT INTO priceitems
                                (part_id, price, currency_id, amount, delivery, supplier_id, operation_id)
                                VALUES %s""" % sets
                try:
                    self.my_db.execute(sql)
                    self.con.commit()
                    self._log.debug('commit price items')
                except BaseException as e:
                    self._log.warning('error in price items: %s' % e)
                    self.con.rollback()
                time.sleep(5)
            return True
        else:
            sets = setss

            if sets == '':
                return False

            sql = """ INSERT INTO priceitems
                            (part_id, price, currency_id, amount, delivery, supplier_id, operation_id)
                            VALUES %s""" % sets
            try:
                self.my_db.execute(sql)
                self.con.commit()
                self._log.debug('commit price items')
            except BaseException as e:
                self._log.warning('error in price items: %s' % e)
                self.con.rollback()

            return True

    def search_brands(self, brands):

        len_set_brand = 0
        if len(brands) == 1:
            brands.append('')

        q = """ SELECT * FROM tof_suppliers """

        self.my_db.execute(q)
        result = self.my_db.fetchall()

        res = [i for j in result for i in (j[1], j[2], j[3]) if i is not None]

        all_result = set(brands) & set(res)
        if all_result is None or len(all_result) == 0:
            return False
        else:
            sets_brands = ''
            for i in result:
                if all_result & set(i):
                    sets_brands += str((i[2], i[1], self.operation_id, int(i[0]))) + ', '
                    len_set_brand += 1
            sql = """INSERT INTO brands
                        (brand, brand_long, operation_id, tecdoc_id)
                        VALUES %s""" % sets_brands[:-2]

            try:
                self.my_db.execute(sql)
                self.con.commit()
                self._log.debug('add new brands: %d' % len_set_brand)
            except BaseException as e:
                self._log.warning('error in new brand: %s' % e)
                self.con.rollback()

        return True

    def search_article(self, article):
        if len(article) == 1:
            article.append('')

        if len(article) == 0:
            return False

        q = """select a.article_nr, b.brand, b.brand_short, a.description, a.id, a.art from tof_articles a
               LEFT JOIN tof_suppliers b ON b.id = a.supplier_id
               where a.article_nr IN  %s""" % str(tuple(article))

        self.my_db.execute(q)
        result = self.my_db.fetchall()
        print '2222'
        if result is None:
            return False
        else:
            new_articles = {}

            for i in result:
                if i[0] in self.all_result:
                    if type(self.all_result[i[0]]) == list:
                        for it in self.all_result[i[0]]:
                            if it['id'] == i[4]:
                                continue
                            else:
                                if i[0] in new_articles:
                                    if type(new_articles[i[0]]) != list:
                                        new_articles[i[0]] = [new_articles[i[0]]]

                                    new_articles[i[0]].append({'id': i[4], 'name': i[3], 'art': i[5], 'brand': i[1], 'br_sh': i[2]})
                                else:
                                    new_articles[i[0]] = {'id': i[4], 'name': i[3], 'art': i[5], 'brand': i[1], 'br_sh': i[2]}
                    else:
                        if self.all_result[i[0]]['id'] == i[4]:
                            continue
                        else:
                            if i[0] in new_articles:
                                if type(new_articles[i[0]]) != list:
                                    new_articles[i[0]] = [new_articles[i[0]]]

                                new_articles[i[0]].append(
                                    {'id': i[4], 'name': i[3], 'art': i[5], 'brand': i[1], 'br_sh': i[2]})
                            else:
                                new_articles[i[0]] = {'id': i[4], 'name': i[3], 'art': i[5], 'brand': i[1],
                                                      'br_sh': i[2]}


            self.new_articles = new_articles

        # sql = """ INSERT INTO parts
        #             (operation_id, tecdoc_id, article, article_long, brand, brand_long, name)
        #             VALUES %s""" % str((self.operation_id, result[0], result[1], result[2], brand, brand, result[4]))

        return True

    def update_articles(self):
        if self.new_articles is None:
            return False

        set_art = ''
        len_set_art = 0
        news_set = set(self.new_articles.keys())

        for i in self._frame:

            item = ''
            if {i[self.article_id]} & news_set:
                if type(self.new_articles[i[self.article_id]]) == list:
                    pos = self.new_articles[i[self.article_id]]
                    for items in pos:
                        item = ''
                        if (i[self.brand_id] in items['brand']) or (i[self.brand_id] in items['br_sh']):
                            item += str((int(self.operation_id), int(items['id']),
                                         str(items['art']), str(i[self.article_id]),
                                         str(items['br_sh']), str(items['brand']), '%s')) % items['name'] + ', '
                            set_art += item

                else:
                    if (str(i[self.brand_id]) in self.new_articles[i[self.article_id]]['brand']) or \
                            (str(i[self.brand_id]) in self.new_articles[i[self.article_id]]['br_sh']):
                        item += str((int(self.operation_id), int(self.new_articles[i[self.article_id]]['id']),
                                     str(self.new_articles[i[self.article_id]]['art']), str(i[self.article_id]),
                                     str(self.new_articles[i[self.article_id]]['br_sh']),
                                     str(self.new_articles[i[self.article_id]]['brand']), '%s')) % \
                                self.new_articles[i[self.article_id]]['name'] + ', '

                        set_art += item
                len_set_art += 1

        set_art = set_art[:-2]

        if set_art == '':
            return False

        sql = """ INSERT INTO parts
                    (operation_id, tecdoc_id, article, article_long, brand, brand_long, name)
                    VALUES %s""" % set_art

        try:
            self.my_db.execute(sql)
            self.con.commit()
            self._log.debug('add new article: %d' % len_set_art)
        except BaseException as e:
            self._log.warning('error in new article: %s' % e)
            self.con.rollback()

        return True

    def iterator(self, brand, article):
        self._log.debug('processing')
        price_items_massive = []
        price_items_set = ''
        bad_reason_brand = ''
        bad_reason_article = ''
        bad_brand = 'bad_brand'
        bad_article = 'bad_article'

        brands_in_system, articles_in_system = self.func_params_from_mysql(brand, article)
        brand_systems = set(brands_in_system)
        articles_systems = set(articles_in_system.keys())

        ggb, gb, bb, ba, be = 0, 0, 0, 0, 0

        try:
            self._log.debug('START iterator')

            temp_article = ''
            for i in self._frame:

                if self.encoding == 2:
                    name = str(i[self.name_id]).decode('cp1251').encode('utf-8').replace("'", "").replace("`", '')
                elif self.encoding == 1:
                    name = str(i[self.name_id]).encode('utf-8').replace("'", "").replace("`", '')

                reason = ''
                if not {str(i[self.brand_id])} & brand_systems:
                    reason = bad_brand

                    if str(i[self.brand_id]) is None or len(str(i[self.brand_id])) == 0:
                        continue
                    if name[0] == '=':
                        continue
                elif not {i[self.article_id]} & articles_systems:
                    reason = bad_article
                    if name[0] == '=':
                        continue

                    if str(i[self.article_id]) is None or len(str(i[self.article_id])) == 0:
                        continue

                for variant in self.variants:
                    item = ''
                    self.count_id = int(variant['count'])
                    if self.is_csv:
                        self.count_id -= 1

                    amount = re.sub("[^0-9]", "", str(i[self.count_id]))
                    if amount == '' or int(amount) == 0 or not str(i[self.price_id]).strip():
                        continue

                    if 'delivery_column' in variant:
                        if self.is_csv:
                            dev = i[int(variant['delivery_column']) - 1]
                        else:
                            dev = i[int(variant['delivery_column'])]
                    else:
                        dev = int(int(variant['delivery_const']))
                    if reason != '' and temp_article != str(i[self.article_id]):
                        try:
                            item += str((str(i[self.brand_id]), str(i[self.article_id]), '%s',
                                         float(i[self.price_id].replace(',', '.').replace(' ', ''))
                                         if type(i[self.price_id]) in (str, unicode)
                                         else float(i[self.price_id]), self.currency, int(amount),
                                         int(dev), self.supplier, self.operation_id, reason)) % name + ', '

                            temp_article = {i[self.article_id]}

                        except BaseException:

                            if not isinstance(dev, int):
                                if dev == '*' or dev is None:
                                    reason = 'else'
                                    delivery = dev
                                else:
                                    delivery = re.sub("[^0-9]", "", dev)

                            else:
                                delivery = dev

                            if reason != 'else':
                                try:
                                    item += str((str(i[self.brand_id]), str(i[self.article_id]), '%s',
                                                 float(i[self.price_id].replace(',', '.').replace(' ', ''))
                                                 if type(i[self.price_id]) in (str, unicode)
                                                 else float(i[self.price_id]), self.currency, amount, delivery,
                                                 self.supplier, self.operation_id, reason)) % name + ', '
                                except:
                                    continue
                    else:

                        if type(articles_in_system[i[self.article_id]]) == list:
                            for items in articles_in_system[i[self.article_id]]:

                                part_id = items['id']
                                brand = items['brand']
                                brand_long = items['brand_long']

                                if (str(i[self.brand_id]).encode() in brand or str(
                                        i[self.brand_id]).encode() in brand_long) or \
                                        (brand in str(i[self.brand_id]).encode() or brand_long in str(
                                        i[self.brand_id]).encode()):

                                    try:
                                        item += str((int(part_id),
                                                     float(i[self.price_id].replace(',', '.').replace(' ', ''))
                                                     if type(i[self.price_id]) in (str, unicode)
                                                     else float(i[self.price_id]),
                                                     self.currency, int(amount), int(dev), self.supplier,
                                                     self.operation_id)) + ', '

                                    except BaseException:

                                        if not isinstance(dev, int):
                                            if dev == '*' or dev is None:
                                                reason = 'else'
                                                delivery = dev
                                            else:
                                                delivery = re.sub("[^0-9]", "", dev)
                                        else:
                                            delivery = dev

                                        if reason != 'else':
                                            try:
                                                item += str((int(part_id),
                                                             float(i[self.price_id].replace(',', '.').replace(' ', ''))
                                                             if type(i[self.price_id]) in (str, unicode)
                                                             else float(i[self.price_id]),
                                                             self.currency, amount, delivery, self.supplier,
                                                             self.operation_id)) + ', '
                                            except:
                                                continue

                        else:

                            brand = articles_in_system[i[self.article_id]]['brand']
                            brand_long = articles_in_system[i[self.article_id]]['brand_long']

                            if (str(i[self.brand_id]).encode() in brand or str(
                                    i[self.brand_id]).encode() in brand_long) or \
                                    (brand in str(i[self.brand_id]).encode() or brand_long in str(
                                        i[self.brand_id]).encode()):

                                try:

                                    item += str((int(articles_in_system[i[self.article_id]]['id']),
                                                 float(i[self.price_id].replace(',', '.').replace(' ', ''))
                                                 if type(i[self.price_id]) in (str, unicode)
                                                 else float(i[self.price_id]), self.currency, int(amount), int(dev),
                                                 self.supplier, self.operation_id)) + ', '

                                except BaseException:

                                    if not isinstance(dev, int):
                                        if dev == '*' or dev is None:
                                            reason = 'else'
                                            delivery = dev
                                        else:
                                            delivery = re.sub("[^0-9]", "", dev)
                                    else:
                                        delivery = dev

                                    if reason != 'else':
                                        try:

                                            item += str((int(articles_in_system[i[self.article_id]]['id']),
                                                         float(i[self.price_id].replace(',', '.').replace(' ', ''))
                                                         if type(i[self.price_id]) in (str, unicode)
                                                         else float(i[self.price_id]),
                                                         self.currency, amount, delivery, self.supplier,
                                                         self.operation_id)) + ', '
                                        except:
                                            continue

                    if item == '':
                        reason = bad_article
                        try:
                            item += str((str(i[self.brand_id]), str(i[self.article_id]), '%s',
                                         float(i[self.price_id].replace(',', '.').replace(' ', ''))
                                         if type(i[self.price_id]) in (str, unicode)
                                         else float(i[self.price_id]), self.currency, amount, int(dev),
                                         self.supplier, self.operation_id, reason)) % name + ', '
                        except:
                            continue

                    if reason == '':
                        price_items_set += item
                        gb += 1
                        if gb > 250000:
                            price_items_massive.append(price_items_set[:-2])
                            price_items_set = ''
                            ggb += gb
                            gb = 0
                    elif reason == bad_brand:
                        bad_reason_brand += item
                        bb += 1
                    elif reason == bad_article:
                        bad_reason_article += item
                        ba += 1
                    else:
                        be += 1

        except BaseException as e:
            print i
            self._log.warning('error in iterator')
            self._log.warning('error: %s' % e)
            raise e

        self._log.debug('FINISH iterator')
        if ggb != 0:
            ggb += gb
            price_items_massive.append(price_items_set[:-2])
            self._log.debug('Bad_brand: %d, bad_article: %d, bad_else: %d, good items: %d' % (bb, ba, be, ggb))
        else:
            self._log.debug('Bad_brand: %d, bad_article: %d, bad_else: %d, good items: %d' % (bb, ba, be, gb))

        self.func_bad_position(bad_reason_brand[:-2], bad_reason_article[:-2])
        if len(price_items_massive) > 0:
            self.func_add_to_priceitems(price_items_massive)
        else:
            self.func_add_to_priceitems(price_items_set[:-2])

        self.con.close()
        return True

    def func_to_csv(self):
        """
        numbers columns begin for 0
        :return:
        """
        self._log.debug('func_to_csv')
        # self.count_id -= 1
        self.article_id -= 1
        self.price_id -= 1
        self.brand_id -= 1
        self.name_id -= 1
        self.is_csv = True
        self.first_id -= 1
        frame = pd.read_csv(self._directory + self._name, sep=';')

        frame.fillna(' ', inplace=True)
        res = self.get_new_frame(frame)

        return res

    ######################################################################################################
    def func_to_txt(self):
        self._log.debug('func_to_txt')

        frame = pd.read_csv(self._directory + self._name, sep=';', header=None, engine='python')[self.first_id:]
        frame.fillna(' ', inplace=True)
        res = self.get_new_frame(frame)
        return res

    ######################################################################################################
    def func_to_excel(self):
        """
        number of columns begin for 1
        :return:
        """
        self._log.debug('func_to_excel')
        # if self.supplier == 10:
        #     frame = pd.read_excel(self._directory + self._name, sheetname='PriceList')
        # else:
        #     frame = pd.read_excel(self._directory + self._name)
        frame = pd.read_excel(self._directory + self._name)

        frame.fillna(' ', inplace=True)
        res = self.get_new_frame(frame)

        return res

    def get_new_frame(self, frame):
        self._log.debug('get new frame')
        bad_symbols = [' ', '-', '/', '_', '.', '=', "'", '\\', ',', '?', '*', '#', '(', ')', '\\\\']
        brands = set([])
        n = 0
        brand_replace = {}
        for i in frame.itertuples():
            if n < self.first_id:
                n += 1
                continue
            print i[self.brand_id]
            print i[self.article_id]
            name_brand = str(i[self.brand_id]).strip(' \t\n\r').lower()
            brand_name = [x for x in name_brand]
            if set(brand_name) & set(bad_symbols):
                    for x in set(brand_name) & set(bad_symbols):
                        name_brand = name_brand.replace(x, "")

            brands.add(name_brand)
            brand_replace[str(i[self.brand_id])] = name_brand

        if np.nan in brands:
            brands.remove(np.nan)

        # brand = [str(i).encode() for i in brands]
        brand = []
        for i in brands:
            if self.encoding == 2:
                bb = str(i).decode('cp1251').encode('utf-8')
            elif self.encoding == 1:
                bb = str(i).encode('utf-8').replace("'", "")
                #bb = str(i).encode('utf-8').replace("'", "").replace("`", '')
            else:
                bb = i
            brand.append(bb)
        if len(brand) < 2:
            brand.append('')

        q = """ select a.brand,a.brand_long, a.change_to_short from brands a
                where a.brand IN %s""" % str(tuple(brand))

        self.my_db.execute(q)
        result_brand = self.my_db.fetchall()
        rules_for_change_brand = {}
        print 'asdadadad'
        for item in result_brand:
            if item[2] is None:
                rules_for_change_brand[str(item[0])] = item[0]
            else:
                rules_for_change_brand[str(item[0])] = item[2]

        brand = [x for y, x in rules_for_change_brand.iteritems()]

        q = """ select a.brand,a.brand_long, a.change_to_short, b.`type`, b.value from brands a
                left join brandrules b ON b.brand_id = a.id
                where a.brand IN %s""" % str(tuple(brand))
        print '1111111111111'
        self.my_db.execute(q)
        result = self.my_db.fetchall()
        all_rules_in_db = []

        for i in result:
            if i[2] is None and i[3] is None and i[4] is None:
                continue
            else:
                all_rules_in_db.append(i)
        brand_rules = {}
        print 'sssssssssssss'
        for x in all_rules_in_db:
            if x[0] in brand_rules:
                brand_rules[x[0]]['options'].append({'rules': x[3], 'values': x[4]})
            else:
                brand_rules[x[0]] = {'brand': x[2], 'options': [{'rules': x[3], 'values': x[4]}]}

        all_frame = []
        n = 0
        for i in frame.itertuples():
            if n < self.first_id:
                n += 1
                continue

            items = [x for x in i]
            rules_for_brand = None
            brand_name_in_file = brand_replace[str(i[self.brand_id])]

            if brand_name_in_file in rules_for_change_brand:
                brand_name_in_file = rules_for_change_brand[str(brand_name_in_file)]

            if brand_name_in_file in brand_rules:
                rules_for_brand = brand_rules

            if rules_for_brand is None:
                items[self.brand_id] = brand_name_in_file

                article_in_file = str(i[self.article_id]).strip(' \t\n\r').replace('"', '').replace("'", '')
                art_file = [x for x in article_in_file]

                if set(art_file) & set(bad_symbols):
                    for x in set(art_file) & set(bad_symbols):
                        article_in_file = article_in_file.replace(x, "")

                items[self.article_id] = article_in_file.lower()
                all_frame.append(tuple(items))
            else:
                items[self.brand_id] = brand_name_in_file
                article_in_file = str(i[self.article_id]).strip(' \t\n\r').replace('"', '').replace("'", '')
                art_file = [x for x in article_in_file]

                if set(art_file) & set(bad_symbols):
                    for x_in in set(art_file) & set(bad_symbols):
                        article_in_file = article_in_file.replace(x_in, "")

                for rul in rules_for_brand[brand_name_in_file]['options']:
                    if (rul['values'] or rul['rules']) is None:
                        continue
                    index = len(rul['values'])
                    if rul['rules'] == 'delete_end' and article_in_file[-index:] == rul['values']:
                        article_in_file = article_in_file[:-index]
                    elif rul['rules'] == 'delete_start' and (article_in_file[:index] == rul['values'] or article_in_file[:index] == rul['values'].lower()):
                        article_in_file = article_in_file[index:]

                items[self.article_id] = str(article_in_file.lower())
                all_frame.append(tuple(items))

        self._log.debug('Frame: %d' % len(all_frame))
        self._frame = all_frame

        articles = set([])
        brands = set([])

        for i in self._frame:
            articles.add(i[self.article_id])
            brands.add(str(i[self.brand_id]).strip(' \t\n\r'))

        # brand = [str(i).strip().encode() for i in brands]
        brand = []
        for i in brands:
            if self.encoding == 2:
                bb = str(i).decode('cp1251').encode('utf-8')
            elif self.encoding == 1:
                bb = str(i).encode('utf-8').replace("'", "")
            else:
                bb = i
            brand.append(bb)

        # article = [str(i).replace(u'\xa0', u' ').strip().encode() for i in articles]
        article = []
        for i in articles:
            if self.encoding == 2:
                bb = str(i).decode('cp1251').encode('utf-8')
            elif self.encoding == 1:
                bb = str(i).encode('utf-8').replace("'", "").replace("`", '')
            else:
                bb = i
            article.append(bb)

        res = self.iterator(brand, article)
        self._log.debug('Size of price: %s' % str(frame.shape))

        return res
