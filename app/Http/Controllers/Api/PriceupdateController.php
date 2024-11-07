<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use DateTime;

class PriceupdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$posts = Price::all();

        /*return response()->json([
            'status' => true,
            
        ]);*/
        // Pass the fetched data to a view
        //return view('priceupdate', ['data' => $body]);
        return view('priceupdate');
		
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function store(Request $request)
    {

		if ($request->hasFile('file') && $request->password!="") {	
		  if($request->password != "Futureecom@!#$"){
			return response()->json([
			  'status' => false,
			  'message' => 'Password Wrong '.$request->password,
		  ], 400);
		  }
		  if ($request->hasFile('file')) {
			$file = $request->file('file');
			// Log or debug the file to check its details
			\Log::info('Uploaded File:', ['name' => $file->getClientOriginalName(), 'size' => $file->getSize()]);
			// Move the uploaded file to a desired location
			$file->move(public_path('uploads'), $file->getClientOriginalName());
			$fil_path=public_path('uploads').'/'.$file->getClientOriginalName();
			$fileContents = file_get_contents($fil_path);
			$fils = json_decode($fileContents);
			
			if (is_array($fils)) {
			  // Access properties of the object
			} else {
			  // Handle the case where $fil is not an object
			  \Log::error('Invalid data type: $fils is not an object');
			  // Optionally, return a response indicating the issue
			  return response()->json(['error' => 'Invalid data type','post'=>$fils], 400);
			}
		   
		  } else {
			return response()->json([
				'status' => false,
				'message' => 'No file uploaded',
			], 400);
		  }  

		  $message ='';
		  $uuid = Str::uuid()->toString();
		  $associated_with_bundles = null;
		  $application=null;
		  $brand_id = null;
		  $brand_name = null;
		  $ca__group_descriptions__instructions_templates = "Instructions";
		  $ca__group_descriptions__overview = "Overview";
		  $ca__group_descriptions__reviews = "Reviews";
		  $ca__group_descriptions__question_answers = "Question and Answers";
		  $ca__group_descriptions__video = "Video";
		  $category_ids = null;
		  $classification = 0;
		  $children_ids = [];
		  $customer_ids = null;
		  $customer_group = null;
		  $default_variant = null;
		  $description = null;
		  $dimensions = null;
		  $extras = [];
		  $gross_price = null;
		  $gross_sale_price = null;
		  $image_url = null;
		  $images = null;
		  $inventory = null;
		  $keep_flat = false;
		  $organisation = 1;
		  $properties = [];
		  $price = null;
		  $real_price = null;
		  $related = [];
		  $sale_price = null;
		  $store = 1;
		  $taxonomy_id = null;
		  $taxonomy_name = null;
		  $taxonomy = null;
		  $tiers = [];
		  $translations = null;
		  $type = null;
		  $variants = [];
		  $tag_ids=null;
		  $parent_id=null;
		  $ca__group_DDI__stock__numer=null;
		  
		  foreach($fils as $fil){
			//delete force
			if (isset($fil->itemdelete) && !is_null($fil->itemdelete)) {
			    DB::table('products')->where('id',$fil->itemdelete)->delete();
			}     
			if (isset($fil->application) && !is_null($fil->application)) {
			    $application = $fil->application;
			}     
			if (isset($fil->image_url) && !is_null($fil->image_url)) {
			    $image_url = $fil->image_url;
			}     
			if (isset($fil->customer_group) && !is_null($fil->customer_group)) {
			    $customer_group = $fil->customer_group;
			}
			if (isset($fil->type) && !is_null($fil->type)) {
			    $type = $fil->type;
			}     
			if (isset($fil->classification) && !is_null($fil->classification)) {
			    $classification = $fil->classification;
			}     
			if (isset($fil->default_variant) && !is_null($fil->default_variant)) {
			    $default_variant = $fil->default_variant;
			} 
			if (isset($fil->associated_with_bundles) && !is_null($fil->associated_with_bundles)) {
			    $associated_with_bundles = $fil->associated_with_bundles;
			} 
			if (isset($fil->keep_flat) && !is_null($fil->keep_flat)) {
				$keep_flat = $fil->keep_flat;
			} 
			if (isset($fil->customer_ids) && !is_null($fil->customer_ids)) {
			    $customer_ids = $fil->customer_ids;
			} 
			if (isset($fil->price) && !is_null($fil->price)) {
			    $price = $fil->price;
			}     
			if (isset($fil->sale_price) && !is_null($fil->sale_price)) {
			    $sale_price = $fil->sale_price;
			}     
			if (isset($fil->gross_price) && !is_null($fil->gross_price)) {
			    $gross_price = $fil->gross_price;
			}     
			if (isset($fil->gross_sale_price) && !is_null($fil->gross_sale_price)) {
			    $gross_sale_price = $fil->gross_sale_price;
			}    
			if (isset($fil->real_price) && !is_null($fil->real_price)) {
				$real_price = $fil->real_price;
			}  
			if (isset($fil->brand_name) && !is_null($fil->brand_name)) {
				$brand_name = $fil->brand_name;
			}     
			if (isset($fil->taxonomy_name) && !is_null($fil->taxonomy_name)) {
			    $taxonomy_name = $fil->taxonomy_name;
			} 
			if (isset($fil->organisation) && !is_null($fil->organisation)) {
			    $organisation = $fil->organisation;
			}     
			if (isset($fil->store) && !is_null($fil->store)) {
			    $store = $fil->store;
			}    
			if (isset($fil->related) && !is_null($fil->related)) {
			    $related = $fil->related;
			} 
			if (isset($fil->inventory) && !is_null($fil->inventory)) {
				$inventory = $fil->inventory;
			}
			if (isset($fil->tiers) && !is_null($fil->tiers)) {
				$tiers = $fil->tiers;
			}
			//above mondatory fields
			if (isset($fil->brand_id) && !is_null($fil->brand_id)) {
				$brand_id = $fil->brand_id;
			}
			if (isset($fil->ca__group_descriptions__overview) && !is_null($fil->ca__group_descriptions__overview)) {
				$ca__group_descriptions__overview = $fil->ca__group_descriptions__overview;
			} 
			if (isset($fil->ca__group_descriptions__reviews) && !is_null($fil->ca__group_descriptions__reviews)) {
				$ca__group_descriptions__reviews = $fil->ca__group_descriptions__reviews;
			} 			   
			if (isset($fil->ca__group_descriptions__question_answers) && !is_null($fil->ca__group_descriptions__question_answers)) {
				$ca__group_descriptions__question_answers = $fil->ca__group_descriptions__question_answers;
			}			   
			if (isset($fil->ca__group_descriptions__instructions_templates) && !is_null($fil->ca__group_descriptions__instructions_templates)) {
				$ca__group_descriptions__instructions_templates = $fil->ca__group_descriptions__instructions_templates;
			}			   
			if (isset($fil->ca__group_descriptions__video) && !is_null($fil->ca__group_descriptions__video)) {
				$ca__group_descriptions__video = $fil->ca__group_descriptions__video;
			} 			   
			if (isset($fil->children_ids) && !is_null($fil->children_ids)) {
			    $children_ids = $fil->children_ids;
			} 
			if (isset($fil->description) && !is_null($fil->description)) {
			    $description = $fil->description;
			}     
			if (isset($fil->dimensions) && !is_null($fil->dimensions)) {
				$dimensions = $fil->dimensions;
			}
			if (isset($fil->extras) && !is_null($fil->extras)) {
			    $extras = $fil->extras;
			}     			  
			if (isset($fil->images) && !is_null($fil->images)) {
				$images = $fil->images;
			}
			if (isset($fil->properties) && !is_null($fil->properties)) {
				$properties = $fil->properties;
			}
			if (isset($fil->taxonomy) && !is_null($fil->taxonomy)) {
				$taxonomy = $fil->taxonomy;
			}
			if (isset($fil->taxonomy_id) && !is_null($fil->taxonomy_id)) {
				$taxonomy_id = $fil->taxonomy_id;
			}
			if (isset($fil->translations) && !is_null($fil->translations)) {
			    $translations = $fil->translations;
			} 
			if (isset($fil->variants) && !is_null($fil->variants)) {
				$variants = $fil->variants;
			}
			if (isset($fil->category_ids) && !is_null($fil->category_ids)) {
				$category_ids = $fil->category_ids;
			}
			if (isset($fil->tag_ids) && !is_null($fil->tag_ids)) {
				$tag_ids = $fil->tag_ids;
			}
			if (isset($fil->parent_id) && !is_null($fil->parent_id)) {
				$parent_id = $fil->parent_id;
			}
			if (isset($fil->ca__group_DDI__stock__numer) && !is_null($fil->ca__group_DDI__stock__numer)) {
				$ca__group_DDI__stock__numer = $fil->ca__group_DDI__stock__numer;
			}
			
			$dateTime = new DateTime();
			$now= $dateTime->format('Y-m-d\TH:i:s.uP');
			$array=[];
			$findItem = DB::table('products')->where('id',$fil->id)->count();
			if($findItem==0){
				$array=array(
				'order' => 0,
				'organisation' => $organisation,
				'store' => $store,
				'gross_price' => $gross_price,
				'sale_price' => $sale_price,
				'gross_sale_price' => $gross_sale_price,
				'classification' => $classification,
				'name' => $fil->name,
				'image_url' => $image_url,
				'sku' => $fil->sku,
				'type' => $type,
				'description' => $description,
				'properties' => $properties,
				'price' => $price,
				'real_price' => $real_price,
				'id' => $uuid,
				'slug' => $fil->slug,
				'created_at' => date("Y-m-d H:i:s"),
				'taxonomy_id' => $taxonomy_id,
				'taxonomy_name' => $taxonomy_name,
				'inventory' => $inventory,
				'category_ids' => $category_ids,
				'brand_id' => $brand_id,
				'brand_name' => $brand_name,
				'extras' => $extras,
				'variants' => $variants,
				'tiers' => $tiers,
				'translations' => $translations,
				'default_variant' => $default_variant,
				'related' => $related,
				'keep_flat' =>$keep_flat,
				'ca__group_descriptions__overview'=>$ca__group_descriptions__overview,
				'ca__group_descriptions__reviews'=>$ca__group_descriptions__reviews,
				'ca__group_descriptions__question_answers'=>$ca__group_descriptions__question_answers,
				'ca__group_descriptions__instructions_templates'=>$ca__group_descriptions__instructions_templates,
				'ca__group_descriptions__video'=>$ca__group_descriptions__video
				);
				if (isset($fil->parent_id) && !is_null($fil->parent_id)) {
					$array["parent_id"] = $fil->parent_id;
				}
				if (isset($fil->associated_with_bundles) && !is_null($fil->associated_with_bundles)) {
					$array["associated_with_bundles"] = $fil->associated_with_bundles;
				}					
				if (isset($fil->children_ids) && !is_null($fil->children_ids)) {
					$array["children_ids"] = $fil->children_ids;
				}
				if (isset($fil->badge_ids) && !is_null($fil->badge_ids)) {
					$array["badge_ids"] = $fil->badge_ids;
				}
									
				DB::table('products')->insert($array);
				$message .= "products insert successfully!";
				
			}else{
			if(property_exists($fil, 'variants')){
			$updates = DB::table('products')->where('id',$fil->id)->orderBy('_id')->update([
				"order"=> 0,
				"slug"=> $fil->slug,
				"image_url"=> $image_url,
				"application"=> $application,
				"customer_group"=> $customer_group,
				"sku"=> $fil->sku,
				"type"=> $type,
				"classification"=> $classification,
				"default_variant"=> $default_variant,
				"associated_with_bundles"=> $associated_with_bundles,
				"keep_flat"=> $keep_flat,
				"customer_ids"=> $customer_ids,
				"price"=> $price,
				"sale_price"=> $sale_price,
				"gross_price"=> $gross_price,
				"gross_sale_price"=> $gross_sale_price,
				"real_price"=> $real_price,
				"brand_name"=> $brand_name,
				"taxonomy_name"=> $taxonomy_name,
				"organisation"=> $organisation,
				"store"=> $store,
				"name"=> $fil->name,
				"related"=> $related,
				"updated_at" => $now,
				"created_at" => $now,
				"inventory" => $inventory,  
				"tiers" => $tiers,  
				"brand_id" => $brand_id,
				"related" => $related,
				"category_ids" => $category_ids,
				"tag_ids" => $tag_ids,
				"taxonomy_id" => $taxonomy_id,
				"extras" => $extras,
				"properties" => $properties,
				"ca__group_descriptions__instructions_templates"=>$ca__group_descriptions__instructions_templates,
				"ca__group_descriptions__overview"=>$ca__group_descriptions__overview,
				"ca__group_descriptions__question_answers"=>$ca__group_descriptions__question_answers,
				"ca__group_descriptions__reviews"=>$ca__group_descriptions__reviews,
				"ca__group_descriptions__video"=>$ca__group_descriptions__video,
				"children_ids" => $children_ids,
				"description" => $description,
				"dimensions" => $dimensions,
				"images" => $images,
				"taxonomy" =>$taxonomy,
				"translations" =>$translations,
				"variants" => $variants,
				"ca__group_DDI__stock__numer" => $ca__group_DDI__stock__numer,
				
				]);
			$existingItem = DB::table('products')->where('id',$fil->id)->count();
			if(count($fil->variants)>0 && $existingItem==1){
				$message .= "products updated successfully!--".$fil->id;
				DB::table('products')->where('parent_id',$fil->id)->delete();
				$vsku = array(); // Initialize the array to store relationships
				$vname= array();
				if(is_object($fil) && property_exists($fil, 'variants') && is_array($fil->variants) && count($fil->variants) > 0) {
				$level1 = $fil->variants[0]->options;
				if(isset($fil->variants[0]->options) && count($fil->variants)==1){
					foreach ($level1 as $item1) {
						array_push($vsku,$fil->sku.  "-".Str::upper($item1)."-10");
						array_push($vname,$fil->name." ".$item1);
					}
				}	
				if(isset($fil->variants[1]->options) && count($fil->variants)==2){
					$level2 = $fil->variants[1]->options;
					// Loop through level 1
					foreach ($level1 as $item1) {
						// Loop through level 2
						foreach ($level2 as $item2) {
							// Print the relation between items from level 1 and level 2
							array_push($vsku, $fil->sku."-".Str::upper($item1."-".$item2)."-10");
							array_push($vname,$fil->sku."-".$item1."-".$item2);
						}
					}
				}
				if(isset($fil->variants[2]->options) && count($fil->variants)==3){
					$level2 = $fil->variants[1]->options;
					$level3 = $fil->variants[2]->options;
					// Loop through level 1
					foreach ($level1 as $item1) {
						// Loop through level 2
						foreach ($level2 as $item2) {
							// Loop through level 3
							foreach ($level3 as $item3) {
								array_push($vsku, $fil->sku."-".Str::upper($item1."-".$item2."-".$item3)."-10");
								array_push($vname,$fil->sku."-".$item1."-".$item2."-".$item3);
							}
						}
					}
				}
				if(isset($fil->variants[3]->options) && count($fil->variants)==4){
						$level2 = $fil->variants[1]->options;
						$level3 = $fil->variants[2]->options;
						$level4 = $fil->variants[3]->options;
						// Loop through level 1
						foreach ($level1 as $item1) {
							// Loop through level 2
							foreach ($level2 as $item2) {
								// Loop through level 3
								foreach ($level3 as $item3) {
									// Loop through level 4
									foreach ($level3 as $item4) {
										array_push($vsku, $fil->sku."-".Str::upper($item1."-".$item2."-".$item3."-".$item4)."-10");
										array_push($vname,$fil->sku."-".$item1."-".$item2."-".$item3."-".$item4);
									}
								}
							}
						}
					}
					if(isset($fil->variants[4]->options) && count($fil->variants)==5){
						$level2 = $fil->variants[1]->options;
						$level3 = $fil->variants[2]->options;
						$level4 = $fil->variants[3]->options;
						$level4 = $fil->variants[4]->options;
						// Loop through level 1
						foreach ($level1 as $item1) {
							// Loop through level 2
							foreach ($level2 as $item2) {
								// Loop through level 3
								foreach ($level3 as $item3) {
									// Loop through level 4
									foreach ($level3 as $item4) {
										// Loop through level 5
										foreach ($level3 as $item5) {
											// Print the relation between items from level 1 and level 2
											array_push($vsku, $fil->sku."-".Str::upper($item1."-".$item2."-".$item3."-".$item4."-".$item5)."-10");
											array_push($vname,$fil->sku."-".$item1."-".$item2."-".$item3."-".$item4."-".$item5);
										}
									}
								}
							}
						}
					}
				}
				foreach($vsku as $r){
					if (isset($fil->ca__group_descriptions__overview) && !is_null($fil->ca__group_descriptions__overview)) {
						$ca__group_descriptions__overview = $fil->ca__group_descriptions__overview;
					}
					$description =null;
					if (isset($fil->description) && !is_null($fil->description)) {
						$description = $fil->description;
					}
					$namestr=substr($r,strlen($fil->sku));
					$namesstr=str_replace("-"," ",$namestr);
					$namestr=Str::upper($namestr);
					$ca__group_descriptions__instructions_templates=null;
					if (isset($fil->ca__group_descriptions__instructions_templates) && !is_null($fil->ca__group_descriptions__instructions_templates)) {
						$ca__group_descriptions__instructions_templates = $fil->ca__group_descriptions__instructions_templates;
					}
					$ca__group_descriptions__question_answers=null;
					if (isset($fil->ca__group_descriptions__question_answers) && !is_null($fil->ca__group_descriptions__question_answers)) {
						$ca__group_descriptions__question_answers = $fil->ca__group_descriptions__question_answers;
					}
					$ca__group_descriptions__reviews=null;
					if (isset($fil->ca__group_descriptions__reviews) && !is_null($fil->ca__group_descriptions__reviews)) {
						$ca__group_descriptions__reviews = $fil->ca__group_descriptions__reviews;
					}
					$ca__group_descriptions__video=null;
					if (isset($fil->ca__group_descriptions__video) && !is_null($fil->ca__group_descriptions__video)) {
						$ca__group_descriptions__video = $fil->ca__group_descriptions__video;
					}
					$array=array(
					'order' => 0,
					'slug' => $r,
					'image_url' => $image_url,
					'application' => $application,
					'customer_group' => $customer_group,
					'sku' => $fil->name.$namestr,
					'type' => $type,
					'classification' => $classification,
					'default_variant' => $default_variant,
					'associated_with_bundles' => $associated_with_bundles,
					'keep_flat' => $keep_flat,
					'customer_ids' => $customer_ids,
					'price' => $price,
					'sale_price' => $sale_price,
					'gross_price' => $gross_price,
					'gross_sale_price' => $gross_sale_price,
					'real_price' => $real_price,
					'brand_name' => $brand_name,
					'taxonomy_name' => $taxonomy_name,
					'ca__group_descriptions__overview' => $ca__group_descriptions__overview,
					'organisation' => $organisation,
					'store' => $store,
					'name' => $fil->name.substr($namesstr,0,-2),
					'related' => $related,
					'description' => $description,
					'id' => $uuid,
					'ca__group_descriptions__instructions_templates' =>$ca__group_descriptions__instructions_templates,
					'ca__group_descriptions__question_answers' => $ca__group_descriptions__question_answers,
					'ca__group_descriptions__reviews' => $ca__group_descriptions__reviews,
					'ca__group_descriptions__video' => $ca__group_descriptions__video,
					'extras' => $extras,
					'updated_at' => $now,
					'created_at' => $now,
					'inventory' => $inventory,
					'tiers' => $tiers,
					'category_ids' => $category_ids,
					'dimensions' => $dimensions,
					'brand_id' => $brand_id,
					);
					$properties=[];
					$expstr=substr($r,strlen($fil->sku));
					$loop=explode("-",$expstr);
					$prop=[];
					foreach($loop as $l){
						foreach($fil->variants as $kp){
							if(in_array($l,$kp->options) ){
								if ( is_numeric($l)) {
									$value = (int) $l;
								}else{
									$value = $l;
								}
								array_push($prop,[									
									  'key'=> $kp->key,
									  'name'=> $kp->name,
									  'value'=> $value
									]);
							}
						}
					}
					if (isset($fil->properties) && !is_null($fil->properties)) {
						$array['properties'] = array_merge($prop,$fil->properties);
					}
					$array["parent_id"] = $fil->id;
					if (isset($fil->dynamic_attributes) && !is_null($fil->dynamic_attributes)) {
						$array["dynamic_attributes"] = $fil->dynamic_attributes;
					}					
					$array["taxonomy_id"]=null;
					if (isset($fil->taxonomy_id) && !is_null($fil->taxonomy_id)) {
						$array["taxonomy_id"] = $fil->taxonomy_id;
					}
					$array["translations"] =null;
					if (isset($fil->translations) && !is_null($fil->translations)) {
						$array["translations"] = $fil->translations;
					}
					if (isset($fil->tag_ids) && !is_null($fil->tag_ids)) {
						$array["tag_ids"] = $fil->tag_ids;
					}
					// Add dynamic_attributes array
					$prop['dynamic_attributes'] = [
						[
							"code" => "group_descriptions",
							"label" => "Group Descriptions",
							"order" => 1,
							"attributes" => [
								[
									"code" => "ca__group_descriptions__overview",
									"label" => "Overview",
									"type" => "text",
									"default" => "Text",
									"validation" => ["string"],
									"order" => 0,
									"configuration" => [
										"searchable" => null,
										"filterable" => false,
										"sortable" => false,
										"translatable" => false,
										"html" => true,
										"readonly" => false,
										"visibility" => ["console", "storefront", "pos"],
										"json" => false
									],
									"description" => ""
								],	
								[
									"code" => "ca__group_descriptions__instructions_templates",
									"label" => "Instructions & Templates",
									"type" => "text",
									"default" => null,
									"validation" => ["string"],
									"order" => 0,
									"configuration" => [
										"searchable" => null,
										"filterable" => false,
										"sortable" => false,
										"translatable" => false,
										"html" => true,
										"readonly" => false,
										"visibility" => ["storefront", "console", "pos"],
										"json" => false
									],
									"description" => ""
								],[
									"code" => "ca__group_descriptions__question_answers",
									"label" => "Question and Answers",
									"type" => "text",
									"default" => "Text",
									"validation" => ["string"],
									"order" => 0,
									"configuration" => [
										"searchable" => null,
										"filterable" => false,
										"sortable" => false,
										"translatable" => false,
										"html" => true,
										"readonly" => false,
										"visibility" => ["console", "storefront", "pos"],
										"json" => false
									],
									"description" => ""
								],	
								[
									"code" => "ca__group_descriptions__reviews",
									"label" => "Reviews",
									"type" => "text",
									"default" => "Text",
									"validation" => ["string"],
									"order" => 0,
									"configuration" => [
										"searchable" => null,
										"filterable" => false,
										"sortable" => false,
										"translatable" => false,
										"html" => true,
										"readonly" => false,
										"visibility" => ["console", "storefront", "pos"],
										"json" => false
									],
									"description" => ""
								],	
								[
									"code" => "ca__group_descriptions__video",
									"label" => "Video",
									"type" => "text",
									"default" => "Text",
									"validation" => ["string"],
									"order" => 0,
									"configuration" => [
										"searchable" => null,
										"filterable" => false,
										"sortable" => false,
										"translatable" => false,
										"html" => true,
										"readonly" => false,
										"visibility" => ["console", "storefront", "pos"],
										"json" => false
									],
									"description" => ""
								]
								// Add more attributes as needed
							]
						]
					];		
					$array['dynamic_attributes']=$prop['dynamic_attributes'];
					DB::table('products')->insert($array);	
					$message .= "products variants updated successfully!---".$uuid;					
					//var_dump($array['properties']);exit;
					}
				
			   }
			
				
			}else{
				$message .= "products Id and Variants Not Found!";
			}
		  }	
		  }
			return response()->json([
			'status' => true,
			'message' => $message,
			'post' => $array
			], 200);
	  }	
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
    }

    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $post->delete();

        return response()->json([
            'status' => true,
            'message' => "Post Deleted successfully!",
        ], 200);
    }
}
