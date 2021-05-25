import i2c_lib
import lcddriver
import sys


lcd = lcddriver.lcd()

def main(linea, linea2):
  # Main program block

    lcd.lcd_clear()
    #print(msg)
    lcd.lcd_display_string(linea[:20], 2)
    lcd.lcd_display_string(linea2[:20], 3)
    
def clearpantalla():
    lcd.lcd_clear()
    
        
if __name__ == '__main__':

    main(linea, linea2)
