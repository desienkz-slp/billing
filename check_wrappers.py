import glob, re
for f in glob.glob('resources/js/Pages/Settings/*.vue'):
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Replace the class
    new_content = re.sub(r'class="max-w-full h-full flex flex-col min-h-0 w-full mx-auto p-4 px-4 sm:px-6 lg:px-8"', 'class="h-full flex flex-col min-h-0 w-full p-2"', content)
    new_content = re.sub(r'class="px-4 sm:px-6 lg:px-8 p-4 w-full max-w-full h-full flex flex-col min-h-0 w-full mx-auto"', 'class="h-full flex flex-col min-h-0 w-full p-2"', new_content)
    
    if content != new_content:
        with open(f, 'w', encoding='utf-8') as file:
            file.write(new_content)
        print(f"Updated {f}")
