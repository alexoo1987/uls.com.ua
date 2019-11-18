#!/usr/bin/python
# -*- coding: utf8 -*-

import sys
import logging
import logging.handlers


class Log:
    """create and use log-file"""
    __shared_state = {}

    #  file_com = None
    #    rr = 0
    def __init__(self, name_file=None):
        """config file log"""
        self.__dict__ = self.__shared_state
        self.formatter = logging.Formatter(fmt='[%(asctime)s] - %(levelname)-4s - %(message)s',
                                           datefmt='%d/%m/%Y %H:%M:%S')

        if name_file is not None:
            # self.file_com = logging.FileHandler(name_file, mode='a', delay=False)
            self.file_com = logging.handlers.RotatingFileHandler(name_file,  maxBytes=1000000, backupCount=5)
            self.file_com.setLevel(logging.DEBUG)
            self.file_com.setFormatter(self.formatter)
            self.console = logging.StreamHandler()
            self.console.setLevel(logging.WARNING)
            self.console.setFormatter(self.formatter)
            self.__logger = logging.getLogger()
            self.__logger.setLevel(logging.DEBUG)
            for handler in [self.console, self.file_com]:
                self.__logger.addHandler(handler)

    def warning(self, str):

        self.__logger.warning(str)
        pass

    def debug(self, str):
        self.__logger.debug(str)

    def info(self, str):
        self.__logger.info(str)

    def error(self, str):
        self.__logger.error(str)



# if __name__ == "__main__":
#     sys.exit(2)
