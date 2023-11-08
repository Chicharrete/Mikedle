import json

# Especifícale la ruta completa al archivo 'champs.json'
ruta_del_archivo = 'C:/xampp/htdocs/Loldle/champs.json'

# Lee el archivo JSON
with open(ruta_del_archivo, 'r') as file:
    data = json.load(file)

# Accede a los títulos de los campeones e imprímelos
campeones = data['data']
for campeon in campeones.values():
    titulo = campeon['title']
    print(titulo)
