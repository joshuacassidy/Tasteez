<?php 
use PHPUnit\Framework\TestCase;
class LikeTest extends TestCase
{
    private $DB_HOST="db4free.net";
    private $DB_USER="nciscript_test";
    private $DB_PASS="letmein123";
    private $DB_NAME="recipes_test";
    private $conn;
    private $testUser;
    private $id = 0;

   
    public function setUp() {
        try {
            $this->conn = new PDO("mysql:host=".$this->DB_HOST.";"."dbname=".$this->DB_NAME,$this->DB_USER,$this->DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            var_dump("Connected successfully"); 
        }catch(PDOException $e) {
            var_dump("Connection failed: " . $e->getMessage());
        }
        $user = new Tasteez\Models\User($this->conn);
        $previousUser = $user->findByName("test", "test");
        $previousUserID = (int) $previousUser["id"];
        if($previousUser != null) {
            $user->deleteUser($previousUserID);
            var_dump("Set up: user Deleted");
        }
        $user->createNew("test", "test", "test");
        var_dump("Set up: user Created");
        $this->testUser = $user->findByName("test", "test");
        $this->id = (int) $this->testUser["id"];
    }

   
    public function testUserLikingTheRecipe() {
       $meal = new Tasteez\Models\Meal($this->conn); 
       $like = new Tasteez\Models\Liked($this->conn); 
       $beforeLike = $like->getMealLikeValue($this->id, 52820);
       var_dump("Before like: " . $beforeLike);
       $meal->likeMeal(52820, $this->id);
       $actualOutcome = $like->getMealLikeValue($this->id, 52820);
       var_dump("After like: " . $actualOutcome);
       $expectedOutcome = 1;
       $this->assertEquals($actualOutcome, $expectedOutcome);
    }

   
    public function testUserDisLikingTheRecipe() {
        $meal = new Tasteez\Models\Meal($this->conn); 
        $like = new Tasteez\Models\Liked($this->conn); 
        $beforeDisLike = $like->getMealLikeValue($this->id, 52809);
        var_dump("Before dislike: " . $beforeDisLike);
        $meal->dislikeMeal(52809, $this->id);
        $actualOutcome = $like->getMealLikeValue($this->id, 52809);
        var_dump("After dislike: " . $actualOutcome);
        $expectedOutcome = -1;
        $this->assertEquals($actualOutcome, $expectedOutcome);
    }

  
    public function testUserLikingTheLikedRecipe() {
        $meal = new Tasteez\Models\Meal($this->conn); 
        $like = new Tasteez\Models\Liked($this->conn); 
        $meal->likeMeal(52820, $this->id);
        $beforeMainLike = $like->getMealLikeValue($this->id, 52820);
        var_dump("Before main like: " . $beforeMainLike);
        $meal->likeMeal(52820, $this->id);
        $actualOutcome = $like->getMealLikeValue($this->id, 52820);
        var_dump("After like: " . $actualOutcome);
        $expectedOutcome = 0;
        $this->assertEquals($actualOutcome, $expectedOutcome);
    }

   
    public function testUserDisLikingTheDislikedRecipe() {
        $meal = new Tasteez\Models\Meal($this->conn); 
        $like = new Tasteez\Models\Liked($this->conn); 
        $meal->dislikeMeal(52809, $this->id);
        $beforeMainDisLike = $like->getMealLikeValue($this->id, 52809);
        var_dump("Before main dislike: " . $beforeMainDisLike);
        $meal->dislikeMeal(52809, $this->id);
        $actualOutcome = $like->getMealLikeValue($this->id, 52809);
        var_dump("After dislike: " . $actualOutcome);
        $expectedOutcome = 0;
        $this->assertEquals($actualOutcome, $expectedOutcome);
    }

   
    public function tearDown() {
        $like = new Tasteez\Models\Liked($this->conn); 
        $like->removeLike($this->id, 52820);
        $like->removeLike($this->id, 52809);
        var_dump("Tear down: like/dislike deleted");
        $user = new Tasteez\Models\User($this->conn);
        $user->deleteUser($this->id);
        var_dump("Tear down: user deleted");
    }
}