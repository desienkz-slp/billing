import glob
from PIL import Image

def clean_alpha():
    files = glob.glob('public/images/modules/*.png')
    for file in files:
        img = Image.open(file).convert("RGBA")
        data = img.getdata()
        newData = []
        for item in data:
            # If alpha is very low, make it 0 to avoid drop-shadow artifacts
            if item[3] < 10:
                newData.append((0, 0, 0, 0))
            else:
                newData.append(item)
        img.putdata(newData)
        img.save(file, "PNG")
        print(f"Cleaned alpha for {file}")

if __name__ == '__main__':
    clean_alpha()
