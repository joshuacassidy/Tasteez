<?php

require './vendor/autoload.php';

echo "Uploading data to database...";

$dotenv = new Dotenv\Dotenv(__DIR__ . "/");
$dotenv->load();
$db = new PDO('mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME'). ";",getenv('DB_USER'),getenv('DB_PASS'));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$mealsJSON = file_get_contents((__DIR__ . "/scrape/data/meals.json"));
$meal_ingredientsJSON = file_get_contents((__DIR__ . "/scrape/data/meal_ingredients.json"));
$ingredientsJSON = file_get_contents((__DIR__ . "/scrape/data/ingredients.json"));
$categoriesJSON = file_get_contents((__DIR__ . "/scrape/data/categories.json"));

$meals = json_decode($mealsJSON);
$ingredients = json_decode($ingredientsJSON);
$meal_ingredients = json_decode($meal_ingredientsJSON);
$categories = json_decode($categoriesJSON);

$db->query("DELETE FROM ingredients;");
$db->query("DELETE FROM meals;");
$db->query("DELETE FROM meal_ingredients;");
$db->query("DELETE FROM categories;");


// INSERT CATEGORIES
foreach($categories as $category) {
  // var_dump($category);
    $name = $category->name;
    $image = $category->image;
    $description = $category->description;

  $query = "INSERT INTO categories (
    category, image, description
  ) VALUES ('${name}', '${image}', '${description}');
  ";
  // echo $query . "\n";
  $query = "INSERT INTO categories (
    category, image, description
  ) VALUES (:name, :image, :description);";
  $stmt = $db->prepare($query);
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':image', $image);
  $stmt->bindParam(':description', $description);
  $stmt->execute();
}

// INSERT INTO meals (id,`name`,`cat`,`category_id`,`instructions`,`area`,`thumbnail`,`video`,`source`) VALUES (52802,'Fish pie','Seafood',8,'.','British','https://www.themealdb.com/images/media/meals/ysxwuq1487323065.jpg','https://www.youtube.com/watch?v=2sX4fCgg-UI','');

// INSERT MEALS
foreach ($meals as $meal) {

  // pull category id off the name
  $catStmt = $db->prepare("SELECT id FROM categories WHERE category = '$meal->cat';");
  $catStmt->execute();
  $category = $catStmt->fetch(PDO::FETCH_ASSOC);
  // echo $category['id'] . "\n";

  if($category == null) {
      $catStmt = $db->prepare("SELECT id FROM categories WHERE category = 'Miscellaneous';");
      $catStmt->execute();
      $category = $catStmt->fetch(PDO::FETCH_ASSOC);
    
  }

  // $meal_id = (int)$meal->id;
  // $name = $meal->name;
  // $cat = $meal->cat;
  // $instructions = addslashes($meal->instructions);
  // $area = $meal->area;
  // $thumbnail = $meal->thumbnail;
  // $video = $meal->video;
  // $source = $meal->source;
  // $dateModified = $meal->dateModified;

  $query = "INSERT INTO meals 
    (
       id, name , category_id , instructions ,
       area ,  thumbnail , video 
    ) VALUES (
      :id, :name, :cat_id, :instruct, :area, :thumbnail,:video
    );";

  $stmt = $db->prepare($query);

  $instructions = addslashes($meal->instructions);
  $stmt->bindParam(':id', $meal->id);
  $stmt->bindParam(':name', $meal->name);
  $stmt->bindParam(':cat_id', $category['id']);
  $stmt->bindParam(':instruct', $instructions);
  $stmt->bindParam(':area', $meal->area);
  $stmt->bindParam(':thumbnail', $meal->thumbnail);
  $stmt->bindParam(':video', $meal->video);
  $stmt->execute();

  // $query = "INSERT INTO meals (
  //   `id`, `name`, `cat`, `category_id` `instructions`, `area`, `thumbnail`, `video`, `source`
  // ) VALUES (${meal_id}, \"${name}\", \"${cat}\", \"${category_id}\", \"${instructions}\", \"${area}\", \"${thumbnail}\", \"${video}\", \"${source}\");
  // ";

  // $db->query($query);
  // echo $query . "\n";
  // var_dump($meal); 
}


// // INSERT INGREDIENTS
foreach ($ingredients as $ingredient) {
  $name = $ingredient->name;
  $id = $ingredient->id;
  $query = "INSERT INTO ingredients (id, name) VALUES ('${id}', '${name}')";
  $db->query($query);
  echo $query . "\n";
}

// // INSERT MEAL_INGREDIENTS
$meal_ingredients = array_unique($meal_ingredients, SORT_REGULAR);
foreach ($meal_ingredients as $meal_ingredient) {
  $meal_id = (int)$meal_ingredient->meal_id;
  $ingredient_ids = $meal_ingredient->ingredient_ids;

  foreach ($ingredient_ids as $id) {
    $query = "INSERT INTO meal_ingredients (meal_id, ingredient_id)
              VALUES (${meal_id}, ${id})";
    $db->query($query);
    echo $query . "\n";
  }
}

echo "Upload Complete...";

echo "Adding Constraints";

$db->query("ALTER TABLE meals ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE;");
$db->query("ALTER TABLE meal_ingredients ADD FOREIGN KEY (meal_id) REFERENCES meals(id) ON DELETE CASCADE, ADD FOREIGN KEY (ingredient_id)  REFERENCES ingredients (id)ON DELETE CASCADE;");
$db->query("ALTER TABLE favorites ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, ADD FOREIGN KEY (recipe_id) REFERENCES meals (id) ON DELETE CASCADE;");
$db->query("ALTER TABLE likes ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, ADD FOREIGN KEY (recipe_id)  REFERENCES meals (id) ON DELETE CASCADE;");
$db->query("ALTER TABLE comments ADD FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, ADD FOREIGN KEY (recipe_id)  REFERENCES meals (id) ON DELETE CASCADE;");

echo "Constraints Added...";

