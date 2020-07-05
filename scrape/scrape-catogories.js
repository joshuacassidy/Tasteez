const fs = require('fs')
const rp = require('request-promise')

function scrapeCatogories(callback) {
    rp('https://www.themealdb.com/api/json/v1/1/categories.php')
        .then(res => {
            const categories = JSON.parse(res).categories.map(cat => {
                return {
                    name: cat.strCategory,
                    image: cat.strCategoryThumb,
                    description: cat.strCategoryDescription

                }
            })
            fs.writeFile('./data/categories.json', JSON.stringify(categories), err => {
                if (err) callback(err)
                callback(null)
            })
        })
        .catch(callback)
}

module.exports = scrapeCatogories