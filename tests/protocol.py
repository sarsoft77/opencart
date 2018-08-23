#!/usr/bin/env python3
# -*- coding: utf-8 -*-

from sys import stdout, stderr, argv 
import requests


class Protocol:
    cookies = {}

    def __init__(self, base_url):
        self.base_url = base_url;

    def get_url(self):
        return self.base_url + "/export/exchange1c.php"
        #return self.base_url + "/export/exchange1c.php?XDEBUG_SESSION_START=ECLIPSE_DBGP"

    def checkauth(self, username, password):
        stdout.write("Команда на сервер: type:sale mode:checkauth\n")
        response = requests.get(self.get_url(),
            params={"type": "sale", "mode": "checkauth"},
            auth=(username, password)
        )

        data = response.text.split("\n")
        status = data[0]

        if status == "success":
            cookie_name = data[1]
            cookie_value = data[2]
            self.cookies[cookie_name] = cookie_value
            return (True, None)

        elif status == "failure":
            message = data[1]
            return (False, "Ошибка: %s \n" % message)

        return (False, "Неизвестный ответ: %s \n" % response.text)

    def query_orders(self):
        stdout.write("Команда на сервер: type:sale mode:query\n")
        response = requests.get(self.get_url(),
            params={"type": "sale", "mode": "query"},
            cookies=self.cookies
        )

        if not response.text.startswith("<?xml"):
            data = response.text.split("\n")
            status = data[0]

            if status == "failure":
                message = data[1]
                return (False, "Ошибка: %s \n" % message)
            else:
                return (False, "Неизвестный ответ: %s \n" % response.text)

        return (True, None)


if __name__ == "__main__":
    """
    Usage:
    $ python3 protocol.py http://website.ru username password
    """

    url = argv[1]
    username = argv[2]
    password = argv[3]
    protocol = Protocol(url)

    success, message = protocol.checkauth(username, password)
    if not success:
        stdout.write(message)
        exit()

    success, message = protocol.query_orders()
    if not success:
        stdout.write(message)
        exit()
    else:
        stdout.write("Ответ сервера:\n")
        stdout.write(message)
       

    stdout.write("Все в порядке!\n")
