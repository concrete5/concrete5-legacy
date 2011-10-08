ccm_setupAttributeKeyCategoryItemSearch = function(searchInstance, akID) {alert("ccm_setupAttributeKeyCategoryItemSearch");
	if(typeof alreadyRun === 'undefined') alreadyRun = Array();
	if(typeof beendone === 'undefined') beendone = Array();
	if(typeof alreadyRun[searchInstance] === 'undefined' || typeof beendone[searchInstance] !== 'undefined') {
		alreadyRun[searchInstance] = 1;
		ccm_setupAdvancedSearch(searchInstance);
		
		$("#ccm-" + searchInstance + "-list-cb-all").live('click', function() {
			if ($(this).is(':checked')) {
				$('.ccm-' + searchInstance + '-list-cb input[type=checkbox]').attr('checked', true);
				$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', false);
			} else {
				$('.ccm-' + searchInstance + '-list-cb input[type=checkbox]').attr('checked', false);
				$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', true);
			}
		});
		$(".ccm-" + searchInstance + "-list-cb input[type=checkbox]").live('click', function() {
			if ($(".ccm-" + searchInstance + "-list-cb input[type=checkbox]:checked").length > 0) {
				$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', false);
			} else {
				$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', true);
			}
		});
		
		// if we're not in the dashboard, add to the multiple operations select menu
	
		$("#ccm-" + searchInstance + "-list-multiple-operations").live('change', function() {
			var action = $(this).val();
			switch(action) {
				case 'choose':
					$("td.ccm-" + searchInstance + "-list-cb input[type=checkbox]:checked").each(function() {
						ccm_triggerSelectAttributeKeyCategoryItem(akID, $(this).closest("tr"));
					});
					jQuery.fn.dialog.closeTop();
					break;
				case "properties": 
					oIDstring = 'searchInstance='+searchInstance+'&akCategoryHandle='+$(this).attr('akCategoryHandle');
					$("td.ccm-" + searchInstance + "-list-cb input[type=checkbox]:checked").each(function() {
						oIDstring=oIDstring+'&newObjectID[]='+$(this).val();
					});
					jQuery.fn.dialog.open({
						width: 630,
						height: 450,
						modal: true,
						href: CCM_TOOLS_PATH +'/bricks/bulk_properties?' + oIDstring,
						title: ccmi18n.properties
					});
					break;	
				case "delete": 
					URIComponents = '{"akCategoryHandle":"' + $(this).attr('akCategoryHandle') + '","akcIDs":[';
					$("td.ccm-" + searchInstance + "-list-cb input[type=checkbox]:checked").each(function() {
						URIComponents = URIComponents + '"' + $(this).val() + '",';
					});
					URIComponents = URIComponents.substring(0, URIComponents.length-1);
					URIComponents = URIComponents + ']}';
					
					jQuery.fn.dialog.open({
						width: 300,
						height: 100,
						modal: true,
						href: CCM_TOOLS_PATH + '/bricks/bulk_delete?searchInstance='+searchInstance+'&akCategoryHandle='+$(this).attr('akCategoryHandle')+'&json=' + encodeURIComponent(URIComponents),
						title: ccmi18n.properties
					});
					break;				
			}
			
			$(this).get(0).selectedIndex = 0;
		});
	
		$("div.ccm-" + searchInstance + "-search-advanced-groups-cb input[type=checkbox]").unbind();
		$("div.ccm-" + searchInstance + "-search-advanced-groups-cb input[type=checkbox]").live('click', function() {
			$("#ccm-" + searchInstance + "-advanced-search").submit();
		});
	}
};

ccm_closeModalRefeshSearch = function(searchInstance) {
	alert("ccm_closeModalRefeshSearch");
	jQuery.fn.dialog.closeTop();
	$("#ccm-" + searchInstance + "-advanced-search").submit();
};
ccm_deleteAndRefeshSearch = function(URIComponents, searchInstance) {
	alert("ccm_deleteAndRefeshSearch");
	$.ajax({
		url: CCM_TOOLS_PATH + '/bricks/bulk_delete?task=delete&json=' + URIComponents
	}).responseText;
	jQuery.fn.dialog.closeTop();
	$("#ccm-" + searchInstance + "-advanced-search").submit();
};




(function(){
	var $ = jQuery,
		undefined,
		console;
		
	if(typeof window.console === "undefined"){
		console = {};
		console.debug = console.info = console.warn = console.error = console.log = function(){};	
	}else{
		console = window.console;	
	}
	
	/* ccm.base.js - eventually 
	 ========================================================= */
	 
	var ccm_widget = {
		_getCreateOptions: function() {
			return $.metadata && this.element.metadata({type:"attr", name:"data-options-"+this.widgetName.toLowerCase()});
		},
		bind:function(){
			arguments[0] = (this.widgetEventPrefix+arguments[0]).toLowerCase();
			this.element.bind.apply(this.element, arguments);
		}
	};
	$.widget("ccm.ccm_widget", ccm_widget);
	
	
	
	var ccm_contextMenu = {
		widgetEventPrefix:"ccm_contextMenu_".toLowerCase(),
		options:{
			position:{
				my:"left bottom",
				at:"left top",
				offset:"-5 5"
			}
		},
		_init:function(){
			var I = this;
			
			if(this.option("autoShow")){
				this.show();
			}
			$(".ccm-menu").live(this.widgetEventPrefix+"_show", function(){
				I.hide();
			});
		},
		show:function(pos){
			var I = this,
				current = $.ccm.ccm_contextMenu.current;
			if(current != this){
				//Hide current context menu
				if(current){
					current.hide();
				}
				this.element.fadeIn(200, function(){
					$("body").one("click", function(){
						I.hide();
					});
				});				
				this.position(pos);
				//Set this as the currently visible context menu
				$.ccm.ccm_contextMenu.current = this;
				this._trigger("show");
			}
		},
		hide:function(){
			var current = $.ccm.ccm_contextMenu.current;
			if(current === this){
				this.element.fadeOut(200);
				$.ccm.ccm_contextMenu.current = null;
				this._trigger("hide");
			}
		},
		position:function(pos){
			var mouse = $.ccm.ccm_contextMenu.mouse,
				opts = pos || $.extend({of:mouse}, this.option("position"));

			this.element.position(opts);
			
		},
		showing:function(){
			return this === $.ccm.ccm_contextMenu.current;
		}
	};
	$.widget("ccm.ccm_contextMenu", $.ccm.ccm_widget, ccm_contextMenu);
	//Store a global for the currently current context menu
	$.ccm.ccm_contextMenu.current = null;
	
	$(window).bind("click mousemove", function(evt){
		$.ccm.ccm_contextMenu.mouse = evt;
	});
	

	/* end ccm.base.js 
	 ======================================================= */
	
	
	
	
	
	//Prototype for the ccm_akcItemSearchForm widget
	var ccm_akcItemSearchForm = {
		widgetEventPrefix:"ccm_akcItemSearchForm_".toLowerCase(),
		options:{
			baseId:null,
			searchInstance:null
		},
		_init:function(){
			var I = this;
			
			I._initForm();			
			I._initSearchFields();
		},
		
		_initForm:function(){
			var I = this;
			
			this.element.ajaxForm({
				beforeSubmit: $.proxy(I._beforeSearchSubmit, I),				
				success: $.proxy(I._onSearchSuccess, I)
			});			
			
		},
		
		_initSearchFields:function(){
			var I = this,
				$fBase = this.getRelEl("-field-base"),
				$fWrap = this.getRelEl("-fields-wrapper"),
				$fAdd = this.getRelEl("-add-option"),
				$fTypeBases = this.getRelEl("-field-base-elements");


			//Setup the add field button
			$fAdd.click(function(){
				$fBase.clone().appendTo($fWrap).slideDown(200).removeAttr("id");
			});
			
			//Setup remove field buttons
			$fWrap.delegate(".ccm-search-remove-option", "click", function(evt){
				var $field = $(this).closest("div");
				$field.slideUp(200, function(){
					$(this).closest("div").remove();
				});				
			});
			
			//Setup field type selects
			$fWrap.delegate(".ccm-input-select", "change", function(evt){
				var $select = $(this),
					$fTypeBase = $fTypeBases.find("[search-field='"+$select.val()+"']"),
					$fContent = $select.closest("table").find("td.ccm-selected-field-content").empty(),
					$field = $fTypeBase.clone().appendTo($fContent).fadeIn();
				
				//Set the hidden field
				$select.next(".ccm-selected-search-field").val($select.val());
				
				//Uniquify element ids
				$field.find("[id]").each(function(){
					this.id += "_"+$fWrap.find(".ccm-search-field").length;
				});
				
				//Setup date fields - this should really be done by whatever generates the fields
				$field.find(".ccm-input-date-wrapper input").datepicker({
					showAnim: 'fadeIn'
				});
			});
			
			
		},
		
		_beforeSearchSubmit:function(){
			this.disable();
		},
		_onSearchSuccess:function(resp){
			
			this.getResultsWidget().element.replaceWith(resp);
			this.enable();
		},
		
		enable:function(){
			this.element.find(":submit").removeAttr("disabled");
			this.element.find(".ccm-search-loading").hide();
			
			return this._setOption( "disabled", false );
		},
		
		disable:function(){
			this.element.find(":submit").attr("disabled","disabled");
			this.element.find(".ccm-search-loading").show();
			
			return this._setOption( "disabled", true );
		},
		
		getResultsWidget:function(){
			return $("#"+this.getBaseId()+"_results").data("ccm_akcItemSearchResults");
		},
		
		getRelEl:function(sel){
			return $(this.getRelElId(sel));
		},
		
		getRelElId:function(sel){
			return "#"+this.element.attr("id")+sel;
		},
		
		getBaseId:function(){
			var id = this.options.baseId || this.element.attr("id").replace(/^(.+)_\w+$/, "$1");	//this is not really necessary...baseId should be a required option for this widget	
			return id;
		},
		
		//bind: ccm_akcItem.bind
	};
	
	$.widget("ccm.ccm_akcItemSearchForm", ccm_akcItemSearchForm);
	
	
	//Prototype for the ccm_akcItemSearchResults widget
	var ccm_akcItemSearchResults = {
		widgetEventPrefix:"ccm_akcItemSearchResults_".toLowerCase(),
		options:{
			baseId:null,
			akcHandle:null,
			searchInstance:null,
			mode:"choose_multiple",
			itemSelector:"tr.ccm-list-record"
		},
		_init:function(){
			var I = this,
				id = this.element.attr("id");
			
			this._initItems();	
			this._initSorting();		
			this._initPaging();
			this._initColumnConfig();			
			
			
		},
		
		_initItems:function(){
			var I = this;
			//Bind to the checkboxes
			this.element.delegate(this.options.itemSelector +" > td > input[name=ID]", "change", function(evt){
				I.execItemAction(this.checked ? "select" : "unselect", $(this).closest("tr"));
			});
			
			//Bind to the item containing rows
			this.element.delegate(this.options.itemSelector +" > td:not(:has(input[name=ID]))", "click", function(evt){
				if(I.options.mode === "choose_multiple" || I.options.mode === "choose" || I.options.mode === "admin"){
					I.execItemAction("choose", $(this).closest("tr"));
				}
			});
			
			//Bind to the "all" checkbox
			this.getRelEl("-list-cb-all").bind("change", function(){				
				I.selectAllToggle(this.checked);
			});
			
			//Bind to the multiple operations checkbox
			this.getRelEl("-list-multiple-operations").bind("change", function(){
				var action = this.value;
				if($.trim(this.value).length){
					I.execItemAction(this.value, I.items(true));
				}
				
			});
			
		},
		
		_initSorting:function(){
			var I = this;
			//Setup ajax sorting
			this.element.find(".ccm-results-list th a").not(".ccm-search-add-column").click(function() {
				$.get($(this).attr("href"), function(data){
					I.element.replaceWith(data);
					$("div.ccm-dialog-content").attr('scrollTop', 0);
				});
				return false;
			});			
		},
		
		_initPaging:function(){
			//Setup ajax paging
			this.element.find("div.ccm-pagination a").click(function() {
				$.get($(this).attr("href"), function(data){
					I.element.replaceWith(data);
					$("div.ccm-dialog-content").attr('scrollTop', 0);
				});
				return false;
			});
		},
		
		_initColumnConfig:function(){
			var I = this;
			//Setup column config
			this.element.find("a.ccm-search-add-column").click(function(){
				var params = {
					searchInstance:I.options.searchInstance,
					baseId:I.options.baseId,
					columns:$('input[name=columns_'+I.options.searchInstance+']').val(),
					persistantBID: $('input[name=persistantBID_'+I.options.searchInstance+']').val()
				};
				jQuery.fn.dialog.open({
					width: 550,
					height: 350,
					modal: false,
					href: $(this).attr('href')+"&"+$.param(params),
					title: ccmi18n.customizeSearch				
				});
				return false;
				
			});
			
		},
		
		
		getFormWidget:function(){
			return $("#"+this.getBaseId()+"_form").data("ccm_akcItemSearchForm");
		},
		
		items:function(ifSelected){
			var id = this.element.attr("id"),
				$items = this.element.find(this.options.itemSelector);
			if(ifSelected === true){
				$items = $items.filter(":has(input[name=ID]:checked)");
			}else if(ifSelected === false){
				$items = $items.not(":has(input[name=ID]:checked)");
			}
			return $items;
		},
		
		selectAllToggle:function(isChecked){			
			this.execItemAction(isChecked ? "select" : "unselect", this.items());
		},
		
		multipleActionsToggle:function(isEnabled){
			var id = this.element.attr("id"),
				$items = this.items(),
				$dropdown = $("#"+id+"-list-multiple-operations");
				
			if(isEnabled === true || ($items.has("input[name=ID]:checked").length && isEnabled !== false)){
				$dropdown.removeAttr("disabled");
			}else{
				$dropdown.attr("disabled","disabled");	
			}
		},
		
		itemActions:{
			select:function(data){
				data.$item.find("input[name=ID]").prop("checked", true);				
				data.widget.multipleActionsToggle();
			},
			unselect:function(data){
				data.$item.find("input[name=ID]").prop("checked", false);
				data.widget.multipleActionsToggle();
			},
			choose:function(data){
				console.log(arguments);
			},
			properties:function(data){
				var params = {
						searchInstance:data.widget.options.searchInstance,
						baseId:data.widget.options.baseId,
						akCategoryHandle:data.widget.options.akcHandle
					},
					ids = data.$item.find("input[name=ID]").map(function(){
						return this.value;
					}).get();
				params["akciID[]"] = ids;
				//console.log($.param(params));
		
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: true,
					href: CCM_TOOLS_PATH +'/bricks/bulk_properties?' + $.param(params),
					title: ccmi18n.properties
				});	
			},
			"delete":function(data){
				var params = {
						searchInstance:data.widget.options.searchInstance,
						baseId:data.widget.options.baseId,
						akCategoryHandle:data.widget.options.akcHandle
					},
					ids = data.$item.find("input[name=ID]").map(function(){
						return this.value;
					}).get();
				params["akciID[]"] = ids;
		
				jQuery.fn.dialog.open({
					width: 300,
					height: 100,
					modal: true,
					href: CCM_TOOLS_PATH +'/bricks/bulk_delete?' + $.param(params),
					title: ccmi18n.properties
				});	
			}
		},
		
		execItemAction:function(key, $item, extraArgs){
			var I = this,
				data = {key:key, $item:$item, widget:I};
			
			//if($.isArray(extraArgs)){
				//data = data.push.apply(data, extraArgs);
				$.extend(data, extraArgs);
			//}
			
			//Execute the action
			var result = null;
			
			//copy the data object
			data = $.extend(true, {}, data);
			
			var args = [key+"item", null, data];
			I._trigger.apply(I, args);			
			
			if($.isFunction(I.itemActions[key])){
				result = I.itemActions[key].apply($item.get(0), [data]);
			}
					
		},
		
		bind:ccm_akcItemSearchForm.bind,		
		getRelEl:ccm_akcItemSearchForm.getRelEl,
		getRelElId:ccm_akcItemSearchForm.getRelElId,
		getBaseId:ccm_akcItemSearchForm.getBaseId
	};
	
	//Register the ccm_akcItemSearchResults widget
	$.widget("ccm.ccm_akcItemSearchResults", ccm_akcItemSearchResults);
	
	
	
	
	
	//Prototype for the jQuery.ccm_akcItemSelector widget
	var ccm_akcItemSelector = {
		widgetEventPrefix:"ccm_akcItemSelector_".toLowerCase(),
		options:{
			max:0,
			baseId:null,
			akcHandle:null,
			searchInstance:null,
			itemTemplate:"<tr data-akciid='${id}' class='ccm-list-record'><td colspan='20'>${id}</td></tr>",
			itemClickAction:"context",
			itemSearchDialog:{
				href:CCM_TOOLS_PATH+"/bricks/search_dialog",
				width:"80%",
				height:"80%",
				modal:false
			},
			itemSearchParams:{
				mode:"choose_multiple"
			},
			itemCreateDialog:{
				href:CCM_TOOLS_PATH+"/bricks/insert_dialog",
				width:"80%",
				height:"80%",
				modal:false
			},
			itemCreateParams:{},
			itemRefreshParams:{
				"columns[]":[]
			},
			itemSelector:"tr.ccm-list-record",
			itemSortableOptions:{axis:"y", delay:300, helper:function(){return $("<tr style='background:black;padding:0;height:5px;overflow:hidden;'><td colspan='100'/></tr>").css({opacity:.4});}}
		},
		_init:function(){
			var I = this,
				baseId = this.getBaseId();
			
			//console.log(this._getCreateOptions());
			
			this._table = this.element.find("table").first();
			this._tbody = this._table.children("tbody");
			//this._itemTemplate = this.items().filter(".template").remove().removeClass("template");
			
			//Compile the item template
			this.option("itemTemplate", $.template(this.option("itemTemplate")));
			
			//Setup the item list wrapper
			this._tbody.sortable(this.option("itemSortableOptions"));
			this._tbody.disableSelection();
			
			this._tbody.bind("sortstop", function(event, ui) {
				I.restripe();
			  	//Not sure what the best way to relay this is yet, or if we need to at all... as item action?
			});
			
			
			//Setup the item selector trigger
			var $thActions = this._table.find("th.list-actions").first();
			$thActions.find(".search").bind("click", function(){
				if(I.options.max == 0 || I._tbody.children(I.options.itemSelector).length < I.options.max){
					I.openItemSearch();
				}
			});
			$thActions.find(".create").bind("click", function(){
				if(I.options.max == 0 || I._tbody.children(I.options.itemSelector).length < I.options.max){
					I.openItemCreate();
				}
			});

			
			//Listen for selected items
			$("#"+baseId+"_results").live(ccm_akcItemSearchResults.widgetEventPrefix+"chooseitem", function(evt, data){
				var ids = data.$item.map(function(){
					var id = $(this).find("input[name=ID]").first().val()
					I.addItem(id, null, true);
					return id;
				});							
				I.refresh(ids);
				jQuery.fn.dialog.closeTop();	
			});
			
			
			this._delegateItemActions(this._tbody, ".item-actions a", "click");
			
			this.items().parent().delegate(this.options.itemSelector, "click", function(evt){
				var $this = $(this),
					$target = $(evt.target);
				if(!$target.hasClass(".item-actions") && !$target.closest(".item-actions").length){
					I.execItemAction(I.option("itemClickAction"), $this);
				}				
			});
			
			$("#"+baseId+"_insert_dialog").live(ccm_akcItemInsertDialogForm.widgetEventPrefix+"complete", function(evt, xhr){
				if(xhr.status === 200){
					jQuery.fn.dialog.closeTop();
					var data = eval("("+xhr.responseText+")");
					I.addItem(data.akciID);					
				}
			});
			
			
			//Prep the UI
			I.refresh(true);
			
		},
		addItem:function(akciID, position, delayRefresh, data){
			data = data || {id:akciID, loading:true};
			
			var $item = $.tmpl(this.option("itemTemplate"), data),
				$existing = this.items().filter("[data-akciid="+akciID+"]");
			
			//Remove the existing item for replacement
			if($existing.length){
				$item = $existing.remove();
			}
			
			//If we've reached the max items, remove the last ones before we add the new one
			var $all,
				max = this.option("max");
			while(max != 0 && ($all = this.items()).length >= max){					
				$all.last().remove();
			}

			//Now add the new item
			if(position == null || position > all.length){
				this._tbody.append($item);
			}else if(position < 1){				
				this._tbody.prepend($item);
			}else{
				$all.eq(position).insertAfter($item);
			}
			
			
			//Update some UI stuff
			if(!delayRefresh){ 
				this.execItemAction("refresh", $item);
				this.refresh(false);
			}			
			
			this._trigger("addItem", null, $item, position);
		},
		
		itemActions:{
			remove:function(data){
				data.$item.fadeOut(300, function(){
					data.$item.remove();
					data.widget.refresh();
				});							
			},
			refresh:function(data){
				var params = {
					akCategoryHandle:data.widget.options.akcHandle
				};
				var ids = data.$item.map(function(){
					return $(this).data("akciid");
				}).get();				
				params["akciID[]"] = ids;
				if(params["akciID[]"].length){
					$.getJSON(CCM_TOOLS_PATH+"/bricks/get_item_properties?"+$.param($.extend(data.widget.options.itemRefreshParams, params)), function(json, status, xhr){
						data.$item.each(function(){
							var $item = $(this),
								id = $item.data("akciid");
							json[id].id = id;
							var $new = $.tmpl(data.widget.option("itemTemplate"), json[id]);
							$item.replaceWith($new);
							$new.find(".ccm-menu").ccm_contextMenu();						
						});
					});
				} 
			},
			properties:function(data){
				var params = {
						searchInstance:data.widget.options.searchInstance,
						baseId:data.widget.options.baseId,
						akCategoryHandle:data.widget.options.akcHandle
					},
					ids = data.$item.map(function(){
						return $(this).data("akciid");
					}).get();
				params["akciID[]"] = ids;
				//console.log($.param(params));
		
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: true,
					href: CCM_TOOLS_PATH +'/bricks/bulk_properties?' + $.param(params),
					title: ccmi18n.properties
				});	
			},
			context:function(data){
				data.$item.last().find(".ccm-menu").first().ccm_contextMenu("show");			
			}
		},		
		
		execItemAction:ccm_akcItemSearchResults.execItemAction,		
		
		_delegateItemActions:function($context, selector, eventName, $item){
			var I = this;
			
			$context.delegate(selector, eventName||"click", function(){
			
				var $a = $(this),
					classes = $a.attr("class") ? $.trim($a.attr("class")).split(/\s+/g) : [],
					$item = $item || $a.closest(I.options.itemSelector);
				
				for(var c = 0; c < classes.length; c++){
					var key = classes[c];
					if($.isFunction(I.itemActions[key])){
						I.execItemAction(key, $item);
					}
				}
				
			});
			
		},
		
		openItemSearch:function(opts, params){
			
			var parameters = $.extend({
					fieldName:this.options.fieldName,
					akCategoryHandle:this.options.akcHandle,
					baseId:this.options.baseId
				}, this.options.itemSearchParams, params),
				dialogOpts = $.extend({}, this.options.itemSearchDialog, opts);
				
			dialogOpts.href += "?"+$.param(parameters);
			
			dialogOpts.onOpen = function(){ };
			dialogOpts.onClose = function(){ };
			
			jQuery.fn.dialog.open(dialogOpts);
			
		},
		
		openItemCreate:function(opts, params){
			var parameters = $.extend({
					akCategoryHandle:this.options.akcHandle,
					baseId:this.options.baseId
				}, this.options.itemCreateParams, params),
				dialogOpts = $.extend({}, this.options.itemCreateDialog, opts);
			
			dialogOpts.href += "?"+$.param(parameters);
			
			jQuery.fn.dialog.open(dialogOpts);
		
		},		
		
		toggleListActions:function(){
			var I = this,
				$thActions = this.element.find("th.list-actions").first();
				$search = $thActions.find(".search"),
				$create = $thActions.find(".create");
				
			if(this.options.max > 0 && this.items().length >= this.options.max){
				$search.add($create).css({opacity:".3"});
			}else{
				$search.add($create).css({opacity:""});
			}
		},
		
		updateCount:function(){			
			this.element.children(".count").children("var").first().html(this.items().length);			
		},
		
		restripe:function(){			
			var $items = this.items();
			$items.removeClass("ccm-list-record-alt").filter(":odd").addClass("ccm-list-record-alt");
			this._trigger("restripe");
		},
		
		items:function(itemIDs){
			var I = this,
				$all = this._tbody.children(I.options.itemSelector);
				$items = null;
			//Filter by IDs, if provided
			if(itemIDs && itemIDs.length){
				itemIDs = $.type(itemIDs) == "array" ? itemIDs : [itemIDs];
				$items = $all.map(function(i, item){
					var $item = $(item),
						$field = $item.find("[name='"+I.options.fieldName+"']");
					if($.inArray(itemIDs, $item.data("akciid"))){
						return item;
					}
				});
			}		
			return $items || $all;
		},
		
		refresh:function(itemIDs){
			if(itemIDs){				
				if(itemIDs === true){
					var $items = this.items();
				}else{
					var $items = this.items(itemIDs);
				}		
				this.execItemAction("refresh", $items);
			}
			this.toggleListActions();
			this.restripe();
			this.updateCount();			
		},
		
		bind:ccm_akcItemSearchForm.bind,		
		getRelEl:ccm_akcItemSearchForm.getRelEl,
		getRelElId:ccm_akcItemSearchForm.getRelElId,
		getBaseId:ccm_akcItemSearchForm.getBaseId,
		
		
	};
	
	
	//Register the jQuery.ccm_akcItemSelector widget
	$.widget("ccm.ccm_akcItemSelector", $.ccm.ccm_widget, ccm_akcItemSelector);
	
	
	
	
	
	
	
	
	var ccm_akcItemInsertDialogForm = {
		widgetEventPrefix:"ccm_akcItemInsertDialogForm_".toLowerCase(),
		options:{},
		_init:function(){
			var I = this;
			//Create the tabset
			this.element.find("> .ccm-dialog-tabs a").click(function(evt){
				var $a = $(this),
					$li = $a.closest("li"),
					idx = $li.prevAll("li").length;
				$li.siblings("li").removeClass("ccm-nav-active");
				$li.addClass("ccm-nav-active");
				I.element.children(".ccm-dialog-tabs-content").eq(idx).show();
				I.element.children(".ccm-dialog-tabs-content").not(":eq("+idx+")").hide();
			});
			
			//Create the ajax form and proxy its events
			this.element.ajaxForm({
				//dataType:"json",
				type:"post",
				beforeSubmit: function(){
					I._trigger.apply(I, ["beforeSubmit", null, arguments]);
				},				
				error: function(){
					I._trigger.apply(I, ["error", null, arguments]);
				},
				success: function(){
					I._trigger.apply(I, ["success", null, arguments]);
				},
				complete: function(xhr, status){
					I.element.replaceWith(xhr.responseText);
					I._trigger.apply(I, ["complete", null, arguments]);
				}
			});
		}
	};
	
	//Register the jQuery.ccm_akcItemInsertDialog widget
	$.widget("ccm.ccm_akcItemInsertDialogForm", ccm_akcItemInsertDialogForm);
	
	
	
	
	var ccm_akcItemDeleteDialogForm = {
		widgetEventPrefix:"ccm_akcItemDeleteDialogForm_".toLowerCase(),
		options:{},
		_init:function(){
			var I = this;			
			
			//Create the ajax form and proxy its events
			this.element.ajaxForm({
				dataType:"json",
				type:"post",
				beforeSubmit: function(){
					I._trigger.apply(I, ["beforeSubmit", null, arguments]);
				},			
				error: function(){
					I._trigger.apply(I, ["error", null, arguments]);
				},
				success: function(){
					I._trigger.apply(I, ["success", null, arguments]);
				},
				complete: function(){
					I.element.replaceWith(xhr.responseText);
					I._trigger.apply(I, ["complete", null, arguments]);
				}
			});
		}
	};
	
	//Register the jQuery.ccm_akcItemInsertDialog widget
	$.widget("ccm.ccm_akcItemDeleteDialogForm", ccm_akcItemDeleteDialogForm);
	

	
})();