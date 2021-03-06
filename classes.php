<?php 
include_once('connection.php');

class Ads{
    private $id;    
    private $title;
    private $description;
    private $name;
    private $email;
    private $phone;
    private $city;
    private $metro;
    private $category_id;
    private $price;
    private $allow_mails;
    protected $type;
    
    public function __construct($ad) {
        if(isset($ad['id'])){
            $this->id=$ad['id'];
        }
        $this->name=$ad['name'];
        $this->type=$ad['type'];
        $this->email=$ad['email'];
        if ( !isset($ad['allow_mails']) ) {
            $this->allow_mails = 0;
        } else {
            $this->allow_mails = $ad['allow_mails'];
        }
        $this->phone=$ad['phone'];
        $this->city=$ad['city'];
        $this->metro=$ad['metro'];
        $this->category_id=$ad['category_id'];
        $this->title=$ad['title'];
        $this->description=$ad['description'];
        $this->price=$ad['price'];
    }

    public function getId() {
        return $this->id;
    }
    public function getName() {
        return $this->name;
    }
    public function getDesc() {
        return $this->description;
    }
    public function getTitle() {
        return $this->title;
    }
    public function getEmail() {
        return $this->email;
    }
    public function getPhone() {
        return $this->phone;
    }
    public function getPrice() {
        return $this->price;
    }
    
    public function save() {
        global $db;
        $vars = get_object_vars($this);
        $db->query('REPLACE INTO adverts(?#) VALUES(?a)',  array_keys($vars),  array_values($vars));
        header("Location: {$_SERVER['PHP_SELF']}");
    }

    public static function deleteAdvert($id) {
        global $db;
        $db->query('DELETE FROM adverts WHERE id=?', $id);
        echo 'The advert with ID number ' . $id . ' was successfully deleted.';
    }

    public function getObjectParam() {
        return get_object_vars($this);
    }
    
    
}

class PrivateAdverts extends Ads {
    public function __construct($ad) {
        parent::__construct($ad);
        $this->type='private';
    }
    
}
class CompanyAdverts extends Ads {
    public function __construct($ad) {
        parent::__construct($ad);
        $this->type='company';
    }
} 

class AdsStore{
    private static $instance=NULL;
    private $ads=array();
    
    public static function instance() {
        if(self::$instance == NULL){
            self::$instance = new AdsStore();
        }
        return self::$instance;
    }
    public function addAds(Ads $ad) {
        if(!($this instanceof AdsStore)){
            die('Нельзя использовать этот метод в конструкторе классов');
        }
        $this->ads[$ad->getId()]=$ad;
    }
    public function getAllAdsFromDb() {
        global $db;
        $all = $db->select('select * from adverts');
        foreach ($all as $value){
            if ($value['type'] == 'private') {
                $ad = new PrivateAdverts($value);
            } elseif ($value['type'] == 'company') {
                $ad = new CompanyAdverts($value);
            }
            self::addAds($ad); //помещаем объекты в хранилище
        }
        return self::$instance;
    }
    public function prepareForOut() {
        global $smarty;
        $row='';
        foreach ($this->ads as $value) {
            $smarty->assign('ad',$value);
            $row.=$smarty->fetch('table_row.tpl.html');
        }
        $smarty->assign('ads_rows',$row);
        return self::$instance;
    }
    public function display() {
        global $smarty;
        $smarty->display('oop.tpl');
    }

    public function getSelects() {
        global $smarty;
        global $db;
        $selects = $db->query("SELECT * FROM select_meta");
        $citys = json_decode($selects[0]['options'], true);
        $smarty->assign('citys', $citys);
        $metro = json_decode($selects[1]['options'], true);
        $smarty->assign('metro1', $metro);
        $categ = json_decode($selects[2]['options'], true);
        $smarty->assign('categorys', $categ);
        return self::$instance;

    }

    public function advertForForm($id) {
        global $smarty;
        $advertForForm = $this->ads[$id];
        foreach ( $advertForForm->getObjectParam() as $key => $value)
        $smarty->assign($key,$value);
    }

}