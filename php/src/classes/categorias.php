<?php
/**
 * Id4Ideas #equipo426
 * https://idforideas.com/
 */


 class categorias
{
	protected static $instance = null;

	/* @var MyPDO */
	protected $db;

	// Properties
	private $fields ;
	private $casts ;
	private $urls ;
	private $orders ;

	public function __construct( )
	{
		$this->db = MyPDO::instance();

		$this->fields = [
			"id" ,
			"nombre" ,
			"imagen" ,
			"orden" ,
		] ;

		$this->casts = [ ] ;

		$this->urls = [
			"imagen" => IMAGES_URL["categorias"],
		] ;

		$this->orders = [
			"id-asc"      => "id ASC" ,
			"id-desc"     => "id DESC" ,
			"nombre-asc"  => "nombre ASC" ,
			"nombre-desc" => "nombre DESC" ,
			"orden-asc"   => "orden ASC" ,
			"orden-desc"  => "orden DESC" ,
		] ;
	}

	// a classical static method to make it universally available
	public static function instance( )
	{
		if (self::$instance === null)
			self::$instance = new self( );

		return self::$instance;
	}

	public function getOne( $id )
	{
		$filter = $this->makeFilter( ["id"=>$id] ) ;

		$sql = [
			"select" => $this->fields,
			"where"  => $filter["where"],
			"vars"   => $filter["params"],
		] ;
		return util::deliverThis( $this->db->getRows("Categorias", $sql)->fetch(), ["casts"=>$this->casts , "urls"=>$this->urls] ) ;
	}

	public function getAll( $extra, $orderId="orden-asc" )
	{
		$filter = $this->makeFilter( $extra ) ;
		$order  = $this->getOrderExpression( $orderId ) ;

		$sql = [
			"select" => $this->fields,
			"where"  => $filter["where"],
			"order"  => $order,
			"vars"   => $filter["params"],
		] ;
		return util::deliverThis( $this->db->getRows("Categorias", $sql)->fetchAll(), ["casts"=>$this->casts , "urls"=>$this->urls] ) ;
	}


	private function makeFilter( $filterCond )
	{

		$filter = [] ;
		$join   = [] ;
		$params = [] ;

		if ( !empty($filterCond) ) {

			foreach ($filterCond as $key => $value)
			{
				if ( $key=="id" ) {
					$filter[] = " id = :id ";

					$params[] = ["name"=>"id" ,"value"=>$value ,"type"=>"INT" ] ;

				}
			}

		}

		return ["join"=>$join, "where"=>$filter, "params"=>$params ] ;

	}


	private function getOrderExpression( $id )
	{
		if ( !empty( $this->orders[$id] ) )
			return $this->orders[$id] ;

		return false ;
	}


	private function getFieldList( )
	{
		return ( is_array($this->fields) ? implode(", ", $this->fields) : $this->fields ) ;
	}


}