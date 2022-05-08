const fs = require('fs')
var path = require('path');
var dirPath = path.resolve(__dirname);

var filesList;
fs.readdir('mods/', function (err, files) {
    filesList = files.filter(function (e) {
        return path.extname(e).toLowerCase() === '.jar'
    });
    let json = []
    filesList.forEach(element => {
        json.push({
            "name": element
        })
    })
    let data = JSON.stringify(json)
    fs.writeFileSync('modsList.json', data)
});
