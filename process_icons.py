import glob
import os
import rembg
from PIL import Image

def process_images():
    files = glob.glob('public/images/modules/*.png')
    for file in files:
        print(f"Processing {file}...")
        try:
            with open(file, 'rb') as i:
                input_data = i.read()
            output_data = rembg.remove(input_data)
            with open(file, 'wb') as o:
                o.write(output_data)
            print(f"Successfully processed {file}")
        except Exception as e:
            print(f"Failed to process {file}: {e}")

if __name__ == '__main__':
    process_images()
