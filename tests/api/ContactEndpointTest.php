<?php 
use PHPUnit\Framework\TestCase;
class ContactEndpointTest extends TestCase
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

    /*
    * @before
    */
    public function setUp() {
        $this->curl = null;
        $this->body = null;
        $this->url = "";
        $this->expectedOutcome = 0;
        $this->statusCode = 0;
        echo "I set up many times\n";
    }

   
    public function testContactApiBody(){
        echo "Running: Get body of contact API endpoint\n";
        $this->url  = "http://localhost:8080/api/contact";
        $this->data = json_encode(array("email" => "test@test.com", "name" => "test", "message" => "test"));
        $this->curl = curl_init();  
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_URL, $this->url );
        $this->body = curl_exec($this->curl);
        curl_close($this->curl);  
        var_dump($this->body);
        $this->assertInternalType('object', json_decode($this->body));
    }
    

    
    public function testContactApiStatus(){
        echo "Running: Get status of contact API endpoint\n";
        $this->url  = "http://localhost:8080/api/contact";
        $this->data = json_encode(array("email" => "test@test.com", "name" => "test", "message" => "test"));
        $this->curl = curl_init();  
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_URL, $this->url );
        $this->body = curl_exec($this->curl);
        $this->statusCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        curl_close($this->curl);  
        $this->expectedOutcome = 200;
        $this->assertEquals($this->statusCode,  $this->expectedOutcome);
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