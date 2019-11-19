import config
import MySQLdb

params = config.price_db


def my_db(log=None):
    if log is not None:
        log.info('Connecting for db')

    con = MySQLdb.connect(user=params['user'],
                          host=params['host'],
                          passwd=params['passwd'],
                          db=params['database'])

    cur = con.cursor()
    con.set_character_set('utf8')
    cur.execute('SET NAMES utf8;')
    cur.execute('SET CHARACTER SET utf8;')
    cur.execute('SET character_set_connection=utf8;')

    return cur, con


if __name__ == '__main__':
    my_db()
