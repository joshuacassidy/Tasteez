<?php 
use PHPUnit\Framework\TestCase;
class LikeDislikeEndpointsTest extends TestCase
{ 
    private $curl;
    private $body;
    private $testUser;
    private $url;
    private $expectedOutcome;
    private $statusCode;
    private $id;
    private $data;
    private $user;
    private $auth;
    private static $conn;
    private $recipeId;

    /*
    * @beforeClass
    */
    public static function setUpBeforeClass() {
        $DB_HOST="db4free.net";
        $DB_USER="nciscript_test";
        $DB_PASS="letmein123";
        $DB_NAME="recipes_test";
        try {
            self::$conn = new PDO("mysql:host=".$DB_HOST.";"."dbname=".$DB_NAME.";charset=UTF8",$DB_USER,$DB_PASS);
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            var_dump("Set up: Connected successfully"); 
        } catch(PDOException $e) {
            var_dump("Set up: Connection failed: " . $e->getMessage());
        }
        echo "I set up once\n";
    }

    /*
    * @before
    */
    public function setUp() {
        $this->curl = null;
        $this->body = null;
        $this->testUser = null;
        $this->url = "";
        $this->expectedOutcome = 0;
        $this->statusCode = 0;
        $this->id = 0;
        $this->recipeId = "52809";
        $this->data = null;
        $this->user = null;
        $this->auth = null;
        $this->curl = curl_init();  
        $this->user = new Tasteez\Models\User(self::$conn);
        $this->auth = new Tasteez\Models\Auth(self::$conn);

        $previousUser = $this->user->findByName("test", "test");
        $previousUserID = (int) $previousUser["id"];

        if($previousUser != null) {
            $this->user->deleteUser($previousUserID);
            var_dump("Set up: user Deleted");
        }

        $this->auth->signUp("test", "test@test.com", "test", "test");
        var_dump("Set up: user Created");
        $this->testUser = $this->user->findByName("test", "test");
        $this->id = (int) $this->testUser["id"];
        echo "I set up many times\n";
    }

  
    public function testLikeRecipeApiBody() {
        echo "Running: Get body of like recipe API endpoint\n";
        $this->data = json_encode(array("email" => "test@test.com", "password" => "test"));
        $this->url = "http://localhost:8080/api/auth/login";
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, COOKIE_FILE);   
	    curl_setopt($this->curl, CURLOPT_COOKIEFILE, COOKIE_FILE);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        $this->body = curl_exec($this->curl);
        $this->url  = "http://localhost:8080/api/meal/" . $this->recipeId ."/like"; 
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_URL, $this->url );
        $this->body = curl_exec($this->curl);
        curl_close($this->curl);
        var_dump($this->body);
        $this->assertInternalType('array', json_decode($this->body));
    }
    
    
    public function testLikeRecipeApiStatus() {
        echo "Running: Get status of like recipe API endpoint\n";
        $this->data = json_encode(array("email" => "test@test.com", "password" => "test"));
        $this->url = "http://localhost:8080/api/auth/login";
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, COOKIE_FILE);   
	    curl_setopt($this->curl, CURLOPT_COOKIEFILE, COOKIE_FILE);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        $this->body = curl_exec($this->curl);
        $this->url  = "http://localhost:8080/api/meal/" . $this->recipeId ."/like"; 
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_URL, $this->url );
        $this->body = curl_exec($this->curl);
        $this->statusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        curl_close($this->curl);
        $this->expectedOutcome = 200;
        $this->assertEquals($this->statusCode, $this->expectedOutcome);
    }
    
    
   
    public function testDislikeRecipeApiBody() {
        echo "Running: Get body of dislike recipe API endpoint\n";
        $this->data = json_encode(array("email" => "test@test.com", "password" => "test"));
        $this->url = "http://localhost:8080/api/auth/login";
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, COOKIE_FILE);   
	    curl_setopt($this->curl, CURLOPT_COOKIEFILE, COOKIE_FILE);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        $this->body = curl_exec($this->curl);
        $this->url  = "http://localhost:8080/api/meal/" . $this->recipeId ."/dislike"; 
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_URL, $this->url );
        $this->body = curl_exec($this->curl);
        curl_close($this->curl);
        var_dump($this->body);
        $this->assertInternalType('array', json_decode($this->body));
    }
     
    public function testDislikeRecipeApiStatus() {
        echo "Running: Get status of dislike recipe API endpoint\n";
        $this->data = json_encode(array("email" => "test@test.com", "password" => "test"));
        $this->url = "http://localhost:8080/api/auth/login";
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, COOKIE_FILE);   
	    curl_setopt($this->curl, CURLOPT_COOKIEFILE, COOKIE_FILE);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_URL, $this->url);
        $this->body = curl_exec($this->curl);
        $this->url  = "http://localhost:8080/api/meal/" . $this->recipeId ."/dislike"; 
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_URL, $this->url );
        $this->body = curl_exec($this->curl);
        $this->statusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        curl_close($this->curl);
        $this->expectedOutcome = 200;
        $this->assertEquals($this->statusCode, $this->expectedOutcome);
    }
   

    /*
    * @afterClass
    */
    public static function tearDownAfterClass() {
        self::$conn = null;
        echo "I teardown once\n";
    }

     /*
    * @after
    */
    public function tearDown() {
        $liked = new Tasteez\Models\Liked(self::$conn);
        $liked->removeLike($this->id, $this->recipeId);
        $this->user->deleteUser($this->id);
        $this->curl = null;
        $this->body = null;
        $this->testUser = null;
        $this->url = "";
        $this->expectedOutcome = 0;
        $this->statusCode = 0;
        $this->id = 0;
        $this->data = null;
        $this->user = null;
        $this->auth = null;
        echo "I teardown many times\n";
    }
}