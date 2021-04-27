"""
Puzzle1_v2
-----------
    Retorna UID en hexadecimal i majuscules d'una targeta Mifare a traves
    d'un lector RFID PN532 connectats amb el bus I2C.

    Utilitza la llibreria py532lib (https://pypi.org/project/py532lib/)
    Bloquejant fins que apropem una targeta.
    Podem fer el metode no bloquejant canviant el parametre de
    set_max_entries() per MIFARE_SAFE_RETRIES.

"""
from py532lib.mifare import * 
import threading


class RfidReader:

    def __init__(self, on_tag):
        self.card = Mifare()
        self.card.SAMconfigure() 
        self.card.set_max_retries(MIFARE_WAIT_FOR_ENTRY)
        self.handler = on_tag
        self.thread = threading.Thread(target=self.handler, daemon=True)
   
    # return uid in hexa str
    def scan_card(self):
        received_uid = self.card.scan_field()
        return received_uid.hex().upper()

    def read_uid(self):
        if not self.thread.is_alive():
            self.thread = threading.Thread(target=self.handler, daemon=True)
            self.thread.start()

