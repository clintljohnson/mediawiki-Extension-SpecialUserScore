<?php

class SpecialUserScore extends QueryPage {
    protected $requestedNamespace = false;

    function __construct($name = 'UserScore') {
        parent::__construct($name);
    }

    function getName() {
        return "UserScore";
    }

    protected function getGroupName() {
        return 'users';
    }

    function isExpensive() {
        return true;
    }

    function isSyndicated() {
        return false;
    }

    function sortDescending() {
        return true;
    }

    function getQueryInfo() {
        return array(
            'tables' => array(
                'user',
                'revision',
                'page'
            ),
            'fields' => array(
                'COUNT(rev_id) as value',
                'COUNT(DISTINCT rev_page) as page_value',
                'user_name as title'
            ),
            'conds' => array(
                'user_id = rev_user',
                'page_id = rev_page',
                'page_namespace = 0'
            ),
            'options' => array(
                'GROUP BY' => 'user_name'
            )
        );
    }

    function getOrderFields() {
        return array(
            'value'
        );
    }

    function formatResult($skin, $result) {
        global $wgContLang;

        $title    = Title::makeTitle(NS_USER, $result->title);
        $userLink = Linker::linkKnown($title, $title->getText());

        $contribLinkText = $this->msg('userscore-result-contrib')->params($result->value, $result->page_value)->parse();
        $contribLink     = Linker::linkKnown(SpecialPage::getTitleFor('Contributions'), $contribLinkText, array(), array(
            'target' => $result->title
        ));
        return $this->msg('userscore-result')->params($userLink, $contribLink)->plain();
    }
}
