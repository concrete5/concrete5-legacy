<?php

$classes = array(
	'Log' => array('library','log'),
	'Request' => array('library','request'),
	'Localization' => array('library','localization'),
	'PageCache' => array('library', 'page_cache/library'),
	'PageCacheRecord' => array('library', 'page_cache/record'),
	'UnknownPageCacheRecord' => array('library', 'page_cache/unknown_record'),
	'FilePageCache' => array('library', 'page_cache/types/file'),
	'View' => array('library','view'),
	'Events' => array('library','events'),
	'Model' => array('library','model'),
	'ItemList' => array('library','item_list'),
	'DatabaseItemList,DatabaseItemListColumnSet' => array('library','database_item_list'),
	'Controller' => array('library','controller'),
	'FileType,FileTypeList' => array('library','file/types'),
	'FileImporter' => array('library', 'file/importer'),
	'BlockView' => array('library','block_view'),
	'MailImporter' => array('library','mail/importer'),
	'Archive' => array('library','archive'),
	'ContentImporter' => array('library','content/importer'),
	'ContentExporter' => array('library','content/exporter'),
	'BlockViewTemplate' => array('library','block_view_template'),
	'BlockController' => array('library','block_controller'),
	'AttributeTypeView' => array('library','attribute/view'),
	'AttributeTypeController' => array('library','attribute/controller'),
	'Marketplace' => array('library', 'marketplace'),
	'Area' => array('model','area'),
	'GlobalArea' => array('model','global_area'),
	'AttributeKey' => array('model','attribute/key'),
	'AttributeValue,AttributeValueList' => array('model','attribute/value'),
	'AttributeKeyCategory' => array('model','attribute/category'),
	'AttributeSet' => array('model','attribute/set'),
	'AttributeType' => array('model','attribute/type'),
	'Block,BlockRecord' => array('model','block'),
	'Job' => array('model','job'),
	'CustomStyleRule,CustomStylePreset' => array('model','custom_style'),
	'File' => array('model','file'),
	'FileSet,FileSetFile,FileSetList' => array('model','file_set'),
	'Pile' => array('model','pile'),
	'FileVersion' => array('model','file_version'),
	'PageList,PageSearchColumnSet' => array('model', 'page_list'),
	'UserList' => array('model', 'user_list'),
	'FileList' => array('model', 'file_list'),
	'UserPrivateMessage,UserPrivateMessageMailbox,UserPrivateMessageList,UserPrivateMessageLimit' => array('model', 'user_private_message'),
	'PageStatistics' => array('model', 'page_statistics'),
	'UserStatistics' => array('model', 'user_statistics'),
	'UsersFriends' => array('model', 'users_friends'),
	'BlockType,BlockTypeList' => array('model','block_types'),
	'Collection' => array('model','collection'),
	'CollectionVersion' => array('model','collection_version'),
	'CollectionType' => array('model','collection_types'),
	'Group,GroupList' => array('model','groups'),
	'GroupSearch' => array('model','search/group'),
	'GroupSet' => array('model','group_set'),
	'GroupSetList' => array('model','group_set_list'),
	'Layout' => array('model','layout'),
	'LayoutPreset' => array('model','layout_preset'),
	'Package,PackageList' => array('model','package'),
	'CollectionAttributeKey,CollectionAttributeValue' => array('model','attribute/categories/collection'),
	'FileAttributeKey,FileAttributeValue' => array('model','attribute/categories/file'),
	'UserAttributeKey,UserAttributeValue' => array('model','attribute/categories/user'),
	'Page' => array('model','page'),
	'SinglePage' => array('model','single_page'),
	'PageTheme,PageThemeEditableStyle' => array('model','page_theme'),
	'ComposerPage' => array('model','composer_page'),
	'PermissionAccess' => array('model','permission/access/model'),
	'PermissionAccessEntity' => array('model','permission/access/entity/model'),
	'PermissionAccessEntityType' => array('model','permission/access/entity/type'),
	'UserPermissionAccessEntity' => array('model','permission/access/entity/types/user'),
	'GroupPermissionAccessEntity' => array('model','permission/access/entity/types/group'),
	'GroupCombinationPermissionAccessEntity' => array('model','permission/access/entity/types/group_combination'),
	'GroupSetPermissionAccessEntity' => array('model','permission/access/entity/types/group_set'),
	'PageOwnerPermissionAccessEntity' => array('model','permission/access/entity/types/page_owner'),
	'FileUploaderPermissionAccessEntity' => array('model','permission/access/entity/types/file_uploader'),
	'PermissionAccessListItem' => array('model','permission/access/list_item'),
	'PermissionDuration' => array('model','permission/duration'),
	'PermissionKeyCategory' => array('model','permission/category'),
	'PermissionKey' => array('model','permission/key'),
	'PermissionAssignment' => array('model','permission/assignment'),
	'Permissions' => array('model','permissions'),
	'PermissionResponse' => array('model','permission/response'),
	'PermissionCache' => array('model','permission/cache'),
	'PermissionSet' => array('model','permission/set'),
	'AdminPermissionKey' => array('model','permission/keys/admin'),
	'AdminPermissionAccess' => array('model','permission/access/categories/admin'),
	'AdminPermissionAccessListItem' => array('model','permission/access/list_items/admin'),
	'BasicPagePermissionAssignment' => array('model','permission/assignments/basic_page'),
	'PagePermissionKey' => array('model','permission/keys/page'),
	'PagePermissionAssignment' => array('model','permission/assignments/page'),
	'PagePermissionAccess' => array('model','permission/access/categories/page'),
	'PagePermissionAccessListItem' => array('model','permission/access/list_items/page'),
	'AreaPermissionKey' => array('model','permission/keys/area'),
	'AreaPermissionAssignment' => array('model','permission/assignments/area'),
	'AreaPermissionAccess' => array('model','permission/access/categories/area'),
	'AreaPermissionAccessListItem' => array('model','permission/access/list_items/area'),
	'BasicWorkflowPermissionKey' => array('model','permission/keys/basic_workflow'),
	'BasicWorkflowPermissionAssignment' => array('model','permission/assignments/basic_workflow'),
	'BasicWorkflowPermissionAccess' => array('model','permission/access/categories/basic_workflow'),
	'BasicWorkflowPermissionAccessListItem' => array('model','permission/access/list_items/basic_workflow'),
	'BlockTypePermissionKey' => array('model','permission/keys/block_type'),
	'BlockTypePermissionAssignment' => array('model','permission/assignments/block_type'),
	'BlockTypePermissionAccess' => array('model','permission/access/categories/block_type'),
	'BlockTypePermissionAccessListItem' => array('model','permission/access/list_items/block_type'),
	'BlockPermissionKey' => array('model','permission/keys/block'),
	'BlockPermissionAssignment' => array('model','permission/assignments/block'),
	'BlockPermissionAccess' => array('model','permission/access/categories/block'),
	'BlockPermissionAccessListItem' => array('model','permission/access/list_items/block'),
	'FileSetPermissionKey' => array('model','permission/keys/file_set'),
	'FileSetPermissionAssignment' => array('model','permission/assignments/file_set'),
	'FileSetPermissionAccess' => array('model','permission/access/categories/file_set'),
	'FileSetPermissionAccessListItem' => array('model','permission/access/list_items/file_set'),
	'FilePermissionKey' => array('model','permission/keys/file'),
	'FilePermissionAssignment' => array('model','permission/assignments/file'),
	'FilePermissionAccess' => array('model','permission/access/categories/file'),
	'FilePermissionAccessListItem' => array('model','permission/access/list_items/file'),
	'MarketplaceNewsflowPermissionKey' => array('model','permission/keys/marketplace_newsflow'),
	'MarketplaceNewsflowPermissionAssignment' => array('model','permission/assignments/marketplace_newsflow'),
	'MarketplaceNewsflowPermissionAccess' => array('model','permission/access/categories/marketplace_newsflow'),
	'MarketplaceNewsflowPermissionAccessListItem' => array('model','permission/access/list_items/marketplace_newsflow'),
	'PagePermissionKey' => array('model','permission/keys/page'),
	'PagePermissionAssignment' => array('model','permission/assignments/page'),
	'PagePermissionAccess' => array('model','permission/access/categories/page'),
	'PagePermissionAccessListItem' => array('model','permission/access/list_items/page'),
	'SinglePagePermissionKey' => array('model','permission/keys/single_page'),
	'SinglePagePermissionAssignment' => array('model','permission/assignments/single_page'),
	'SinglePagePermissionAccess' => array('model','permission/access/categories/single_page'),
	'SinglePagePermissionAccessListItem' => array('model','permission/access/list_items/single_page'),
	'SitemapPermissionKey' => array('model','permission/keys/sitemap'),
	'SitemapPermissionAssignment' => array('model','permission/assignments/sitemap'),
	'SitemapPermissionAccess' => array('model','permission/access/categories/sitemap'),
	'SitemapPermissionAccessListItem' => array('model','permission/access/list_items/sitemap'),
	'StackPermissionKey' => array('model','permission/keys/stack'),
	'StackPermissionAssignment' => array('model','permission/assignments/stack'),
	'StackPermissionAccess' => array('model','permission/access/categories/stack'),
	'StackPermissionAccessListItem' => array('model','permission/access/list_items/stack'),
	'ComposerPagePermissionKey' => array('model','permission/keys/composer_page'),
	'ComposerPagePermissionAssignment' => array('model','permission/assignments/composer_page'),
	'ComposerPagePermissionAccess' => array('model','permission/access/categories/composer_page'),
	'ComposerPagePermissionAccessListItem' => array('model','permission/access/list_items/composer_page'),
	'UserPermissionKey' => array('model','permission/keys/user'),
	'UserPermissionAssignment' => array('model','permission/assignments/user'),
	'UserPermissionAccess' => array('model','permission/access/categories/user'),
	'UserPermissionAccessListItem' => array('model','permission/access/list_items/user'),
	'WorkflowPermissionKey' => array('model','permission/keys/workflow'),
	'WorkflowPermissionAccess' => array('model','permission/access/categories/workflow'),
	'TaskPermission' => array('model','permission/legacy/task'),
	'FilePermissions' => array('model','permission/legacy/file'),
	'PageContentPermissionTimedAssignment' => array('model','permission/assignments/page/timed_content'),
	'AccessUserSearchUserPermissionKey' => array('model','permission/keys/custom/access_user_search'),
	'AccessUserSearchUserPermissionAccess' => array('model','permission/access/categories/custom/access_user_search'),
	'AccessUserSearchUserPermissionAccessListItem' => array('model','permission/access/list_items/custom/access_user_search'),
	'AddBlockBlockTypePermissionKey' => array('model','permission/keys/custom/add_block'),
	'AddBlockBlockTypePermissionAccess' => array('model','permission/access/categories/custom/add_block'),
	'AddBlockBlockTypePermissionAccessListItem' => array('model','permission/access/list_items/custom/add_block'),
	'AddBlockToAreaAreaPermissionKey' => array('model','permission/keys/custom/add_block_to_area'),
	'AddBlockToAreaAreaPermissionAccess' => array('model','permission/access/categories/custom/add_block_to_area'),
	'AddBlockToAreaAreaPermissionAccessListItem' => array('model','permission/access/list_items/custom/add_block_to_area'),
	'AddFileFileSetPermissionKey' => array('model','permission/keys/custom/add_file'),
	'AddFileFileSetPermissionAccess' => array('model','permission/access/categories/custom/add_file'),
	'AddFileFileSetPermissionAccessListItem' => array('model','permission/access/list_items/custom/add_file'),
	'AddSubpagePagePermissionKey' => array('model','permission/keys/custom/add_subpage'),
	'AddSubpagePagePermissionAccess' => array('model','permission/access/categories/custom/add_subpage'),
	'AddSubpagePagePermissionAccessListItem' => array('model','permission/access/list_items/custom/add_subpage'),
	'AssignUserGroupsUserPermissionKey' => array('model','permission/keys/custom/assign_user_groups'),
	'AssignUserGroupsUserPermissionAccess' => array('model','permission/access/categories/custom/assign_user_groups'),
	'AssignUserGroupsUserPermissionAccessListItem' => array('model','permission/access/list_items/custom/assign_user_groups'),
	'EditPagePropertiesPagePermissionKey' => array('model','permission/keys/custom/edit_page_properties'),
	'EditPagePropertiesPagePermissionAccess' => array('model','permission/access/categories/custom/edit_page_properties'),
	'EditPagePropertiesPagePermissionAccessListItem' => array('model','permission/access/list_items/custom/edit_page_properties'),
	'EditPageThemePagePermissionKey' => array('model','permission/keys/custom/edit_page_theme'),
	'EditPageThemePagePermissionAccess' => array('model','permission/access/categories/custom/edit_page_theme'),
	'EditPageThemePagePermissionAccessListItem' => array('model','permission/access/list_items/custom/edit_page_theme'),
	'EditUserPropertiesUserPermissionKey' => array('model','permission/keys/custom/edit_user_properties'),
	'EditUserPropertiesUserPermissionAccess' => array('model','permission/access/categories/custom/edit_user_properties'),
	'EditUserPropertiesUserPermissionAccessListItem' => array('model','permission/access/list_items/custom/edit_user_properties'),
	'ViewUserAttributesUserPermissionKey' => array('model','permission/keys/custom/view_user_attributes'),
	'ViewUserAttributesUserPermissionAccess' => array('model','permission/access/categories/custom/view_user_attributes'),
	'ViewUserAttributesUserPermissionAccessListItem' => array('model','permission/access/list_items/custom/view_user_attributes'),
	'AreaPermissionResponse' => array('model','permission/response/area'),
	'BlockPermissionResponse' => array('model','permission/response/block'),
	'CollectionVersionPermissionResponse' => array('model','permission/response/collection_version'),
	'ComposerPagePermissionResponse' => array('model','permission/response/composer_page'),
	'FileSetPermissionResponse' => array('model','permission/response/file_set'),
	'FilePermissionResponse' => array('model','permission/response/file'),
	'PagePermissionResponse' => array('model','permission/response/page'),
	'SinglePagePermissionResponse' => array('model','permission/response/single_page'),
	'StackPermissionResponse' => array('model','permission/response/stack'),
	'Workflow' => array('model','workflow/model'),
	'EmptyWorkflow' => array('model','workflow/empty'),
	'BasicWorkflow,BasicWorkflowHistoryEntry' => array('model','workflow/types/basic'),
	'BasicWorkflowProgressData' => array('model','workflow/types/basic/data'),
	'WorkflowDescription' => array('model','workflow/description'),
	'WorkflowProgress' => array('model','workflow/progress/model'),
	'WorkflowProgressCategory' => array('model','workflow/progress/category'),
	'WorkflowProgressHistory,WorkflowHistoryEntry' => array('model','workflow/progress/history'),
	'WorkflowProgressResponse' => array('model','workflow/progress/response'),
	'WorkflowProgressAction,WorkflowProgressCancelAction,WorkflowProgressApprovalAction' => array('model','workflow/progress/action'),
	'PageWorkflowProgress' => array('model','workflow/progress/categories/page'),
	'WorkflowRequest' => array('model','workflow/request/model'),
	'PageWorkflowRequest' => array('model','workflow/request/categories/page'),
	'ApprovePagePageWorkflowRequest' => array('model','workflow/request/requests/approve_page'),
	'ChangePagePermissionsPageWorkflowRequest' => array('model','workflow/request/requests/change_page_permissions'),
	'ChangePagePermissionsInheritancePageWorkflowRequest' => array('model','workflow/request/requests/change_page_permissions_inheritance'),
	'ChangeSubpageDefaultsInheritancePageWorkflowRequest' => array('model','workflow/request/requests/change_subpage_defaults_inheritance'),
	'DeletePagePageWorkflowRequest' => array('model','workflow/request/requests/delete_page'),
	'MovePagePageWorkflowRequest' => array('model','workflow/request/requests/move_page'),
	'WorkflowType' => array('model','workflow/type'),
	'User' => array('model','user'),
	'UserInfo' => array('model','userinfo'),
	'UserBannedIP' => array('model','user_banned_ip'),
	'Stack' => array('model','stack/model'),
	'StackList' => array('model','stack/list'),
	'SystemAntispamLibrary' => array('model','system/antispam/library'),
	'SystemCaptchaLibrary' => array('model','system/captcha/library'),
	'SystemCaptchaTypeController' => array('model','system/captcha/controller'),
	'SecurimageSystemCaptchaTypeController' => array('model','system/captcha/types/securimage/controller'),
	'DashboardBaseController' => array('controller', '/dashboard/base')
);

Loader::registerAutoload($classes);
spl_autoload_register(array('Loader', 'autoload'), true);
