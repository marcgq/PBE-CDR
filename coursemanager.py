import gi, os
gi.require_version('Gtk', '3.0')
from gi.repository import Gtk, Gio, Gdk, GdkPixbuf, GLib
from puzzle1_v2 import RfidReader
import lcd
import threading
import requests
import json


class VentanaLogin(Gtk.Box):
        def __init__(self, parent_window):
                Gtk.Box.__init__(self, orientation=Gtk.Orientation.VERTICAL, spacing=0)
                self.__parent_window = parent_window
                self.label = Gtk.Label()
                self.label.set_markup("<span foreground='black' size='x-large'>Please, login with your university card</span>")
                #lcd.clearpantalla()
                #lcd.main("Please, login with", "your university card")
                self.add(self.label)
                self.reader = RfidReader(self.on_tag)
                self.reader.read_uid()
                self.label.set_size_request(600,150)            

        def on_tag(self):
                uid = self.reader.scan_card() 
                contents = requests.get("http://rvd1.upc.edu/proxy/pbe-telem-T2019-marc-garcia-quirantes:55555/?students?uid="+uid)
                dict = contents.json()
                self.__parent_window.name = dict[0]['name']
                self.__parent_window.uid = dict[0]['uid']
                self.__parent_window.query.label2.set_markup("Welcome "+ self.__parent_window.name)
                self.hide()
                #lcd.clearpantalla()
                #lcd.main("Welcome " + self.__parent_window.name)
                self.__parent_window.timer=threading.Timer(20, self.__parent_window.query.logout)
                self.__parent_window.timer.start()
                self.__parent_window.query.show_all()
                
class VentanaQuery(Gtk.Box):
        def __init__(self, parent_window):
                #Creamos la ventana
                Gtk.Box.__init__(self, orientation=Gtk.Orientation.VERTICAL, spacing=0)
                self.__parent_window = parent_window
                self.label2 = Gtk.Label()
                self.button = Gtk.Button(label="Logout")
                self.hbox = Gtk.Box(orientation=Gtk.Orientation.HORIZONTAL, spacing=0)
                self.entry = Gtk.Entry()
                self.entry.connect("activate", self.send_query)
                self.grid = Gtk.Grid()
                self.name = ''
                self.uid=''
                
                self.add(self.hbox)
                self.hbox.pack_start(self.label2, True, True, 0)
                self.hbox.pack_start(self.button, True, False, 0)
                self.add(self.entry)
                self.add(self.grid)
                self.button.connect("clicked", self.logout)      
                self.label2.set_xalign(0)

                
        def logout(self, *widget):
                self.__parent_window.timer.cancel()
                self.__parent_window.query.hide()
                self.__parent_window.login.show_all()
                self.__parent_window.login.reader.read_uid()
                #lcd.clearpantalla()
                #lcd.main("Please, login with", "your university card")
                self.remove(self.grid)
                self.entry.set_text("")
        
        def send_query(self, widget):
                self.__parent_window.timer.cancel()
                contents = requests.get("http://rvd1.upc.edu/proxy/pbe-telem-T2019-marc-garcia-quirantes:55555/?"+self.entry.get_text())
                if (bool(contents)):
                        dict=contents.json()  
                        self.remove(self.grid)
                        self.grid = Gtk.Grid()
                        self.grid.set_column_spacing(20)
                        self.add(self.grid)
                        keys=list(dict[0].keys())
                        self.grid.add(Gtk.Label.new(keys[0]))
                        self.grid.attach(Gtk.Label.new(keys[1]), 1, 0,1, 1)
                        self.grid.attach(Gtk.Label.new(keys[2]), 2, 0,1, 1)
                        i=1
                        if (len(keys)==4):
                                self.grid.attach(Gtk.Label.new(keys[3]), 3, 0,1, 1)
                                for row in dict:
                                        values=list(row.values())
                                        self.grid.attach(Gtk.Label.new(values[0]), 0, i, 1, 1)
                                        self.grid.attach(Gtk.Label.new(values[1]), 1, i, 1, 1)
                                        self.grid.attach(Gtk.Label.new(values[2]), 2, i, 1, 1)
                                        self.grid.attach(Gtk.Label.new(values[3]), 3, i, 1, 1)
                                        i+=1
                                
                        elif (len(keys)==3):
                                for row in dict:
                                        values=list(row.values())
                                        self.grid.attach(Gtk.Label.new(values[0]), 0, i, 1, 1)
                                        self.grid.attach(Gtk.Label.new(values[1]), 1, i, 1, 1)
                                        self.grid.attach(Gtk.Label.new(values[2]), 2, i, 1, 1)
                                        i+=1
                self.show_all()
                self.__parent_window.timer=threading.Timer(20, self.__parent_window.query.logout)
                self.__parent_window.timer.start()
                
class MainWindow(Gtk.Window):
        def __init__(self):
                super(MainWindow, self).__init__(title="Course Manager")
                self.connect("destroy", Gtk.main_quit)
                self.name = ''
                self.uid=''
                self.set_border_width(20)
                self.set_size_request(600, 400)
                

                container = Gtk.Box(orientation=Gtk.Orientation.VERTICAL)
                self.add(container)
                container.show()

                self.login = VentanaLogin(self)
                container.add(self.login)

                self.query = VentanaQuery(self)
                container.add(self.query)  
                
                screen = Gdk.Screen.get_default()
                provider = Gtk.CssProvider()
                provider.load_from_path("coursemanager.css")
                Gtk.StyleContext.add_provider_for_screen(screen, provider, Gtk.STYLE_PROVIDER_PRIORITY_APPLICATION)
             
                self.timer = threading.Timer(20, self.query.logout)

if __name__=="__main__":    
        window=MainWindow()
        window.connect("destroy", Gtk.main_quit)
        window.show_all()
        window.query.hide()
        Gtk.main()
