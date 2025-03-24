\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem
(
    'tt_content',
    'CType',
    [
        'label' => '{%%name%%}',        //The name of the content block
        'description' => '{%%desc%%}',  //The description of the content block
        'value' => '{%%identifier%%}',  //The identifier of the content block
        'icon' => '{%%icon%%}',         //The icon for the content block
        'group' => '{%%group%%}',       //The content block group, 'default' is the standard
    ],
    '{%%placeAt%%}',            //Here comes the identifier of the content block where to insert the new content block in the list
    '{%%position%%}'                    //The positioning can be 'before' or 'after'
);

$GLOBALS['TCA']['tt_content']['types']['{%%identifier%%}'] =
[
    'showitem' => '{%%basicInclude%%}',
    'columnsOverrides' =>
    [
        {%%columnsOverrides%%}
    ]
];