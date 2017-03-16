<?php
namespace RedCat\Autoload;
class LowerCasePSR4{
	protected $namespaces = [];
	protected $checked = [];
	protected $useCache = true;
	protected $useIncludePath = false;
	private static $instance;
	static function getInstance(){
		if(!isset(self::$instance))
			self::$instance = new self;
		return self::$instance;
	}
	function addNamespaces($a){
		foreach($a as $prefix=>$base_dir){
			$this->addNamespace($prefix,$base_dir);
		}
		return $this;
	}
	function addNamespace($prefix, $base_dir, $prepend = false){
		if(is_array($base_dir)){
			foreach($base_dir as $dir){
				$this->addNamespace($prefix, $dir, $prepend);
			}
		}
		else{
			$prefix = trim($prefix, '\\').'\\';
			$base_dir = rtrim($base_dir, '/').'/';
			if(!isset($this->namespaces[$prefix]))
				$this->namespaces[$prefix] = [];
			if ($prepend)
				array_unshift($this->namespaces[$prefix], $base_dir);
			else
				array_push($this->namespaces[$prefix], $base_dir);
		}
		return $this;
	}
	function useCache($b=true){
		$this->useCache = $b;
	}
	function useIncludePath($b=true){
		$this->useIncludePath = $b;
	}
	protected function loadFile($file,$class){
		if(file_exists($file)
			||($this->useIncludePath&&($file=stream_resolve_include_path($file))))
		{
			requireFile($file);
			if(!class_exists($class,false)&&!interface_exists($class,false)&&!trait_exists($class,false))
				throw new \Exception('Class "'.$class.'" not found as expected in "'.$file.'"');
			if($this->useCache)
				$this->checked[] = $class;
			return true;
		}
		return false;
	}
	protected function findRelative($class,$relative_class,$prefix){		
		if(isset($this->namespaces[$prefix])){
			foreach($this->namespaces[$prefix] as $base_dir){
				$file = $base_dir.static::snake(str_replace('\\', '/', $relative_class)).'.php';
				if($this->loadFile($file,$class))
					return true;
			}
		}		
	}
	function findClass($class){
		$prefix = $class;
		while($prefix!='\\'){
			$prefix = rtrim($prefix, '\\');
			$pos = strrpos($prefix, '\\');
			if($pos!==false){
				$prefix = substr($class, 0, $pos + 1);
				$relative_class = substr($class, $pos + 1);
			}
			else{
				$prefix = '\\';
				$relative_class = $class;
			}
			if($this->findRelative($class,$relative_class,$prefix))
				return true;
		}
	}
	function classLoad($class){
		if($this->useCache&&in_array($class,$this->checked))
			return;
		if($this->findClass($class))
			return;
	}
	function __invoke($class){
		return $this->classLoad($class);
	}
	function splRegister(){
		spl_autoload_register([$this,'classLoad']);
	}
	function splUnregister(){
		spl_autoload_unregister([$this,'classLoad']);
	}
	static function snake($str){
        return str_replace(' ', '_', strtolower(preg_replace('/([a-z])([A-Z])/', '$1 $2', $str)));
	}
}

function requireFile(){
	require func_get_arg(0);
}
