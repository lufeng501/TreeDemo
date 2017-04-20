<?php
/**
 * Descript:
 * User: lufeng501206@gmail.com
 * Date: 2017/4/14 11:04
 */

global $data;

$data = require_once "data.php";

/**
 * 重组索引值值
 * @param $data
 * @param string $index
 * @return array
 */
function rebulidDataIndex($data, $index = 'id')
{
    $formatData = [];
    if (!empty($data)) {
        foreach ($data as $key => $value) {
            $formatData[$value[$index]] = $value;
        }
    }
    return $formatData;
}

/**
 * 获取某个节点下的全部数据
 * @param $pid
 * @return array
 */
function getDataLists($pid)
{
    global $data;
    $result = [];
    foreach ($data as $value) {
        if ($value['pid'] == $pid) {
            $result[$value['id']] = $value;
        }
    }
    return $result;
}

/**
 * 遍历获取需要重组树状的列表数据
 * @param int $pid
 * @param int $deep
 * @param array $results
 * @return array
 */
function getTreeLists($pid = 0, $deep = 0, &$results = [])
{
    $lists = getDataLists($pid);
    //array_merge()会重组数组索引值
    //$results = array_merge($results,$lists);
    // + 保留数组索引值
    $results = $results + $lists;
    if (!empty($lists)) {
        $deep++;
        foreach ($lists as $key => $value) {
            if ($deep < 10) {
                getTreeLists($value['id'], $deep, $results);
            }
        }
    }
    return $results;
}

/**
 * 获取树状结构数据(默认索引值)
 * @param $items
 * @return array
 */
function generateTreeDefault($items)
{
    $tree = array();
    foreach ($items as $item) {
        if (isset($items[$item['pid']])) {
            $items[$item['pid']]['children'][] = &$items[$item['id']];
        } else {
            $tree[] = &$items[$item['id']];
        }
    }
    return $tree;
}

/**
 * 获取树状结构数据（以ID作为索引值）
 * @param $items
 * @return array
 */
function generateTreeById($items)
{
    $tree = array();
    foreach ($items as $item) {
        if (isset($items[$item['pid']])) {
            $items[$item['pid']]['children'][$item['id']] = &$items[$item['id']];
        } else {
            $tree[$item['id']] = &$items[$item['id']];
        }
    }
    return $tree;
}

/**
 * 遍历树状结构数据
 * @param int $deep
 * @param array $results
 * @return array
 */
function scanTreeData($tree, $deep = 0, &$results = []){
    // + 保留数组索引值
    if (!empty($tree)) {
        $deep++;
        foreach ($tree as $key => $value) {
            $temp = $value;
            if (isset($temp['children'])) {
                unset($temp['children']);
            }
            $results[] = $temp;
            if ($deep < 10) {
                if (isset($value['children']) && !empty($value['children'])) {
                    scanTreeData($value['children'], $deep, $results);
                }
            }
        }
    }
    return $results;
}

$items = getTreeLists();

// 如果$items索引值被重组了就要重新组装索引值
//$items = rebulidDataIndex($items);

$treeDefault = generateTreeDefault($items);

$treeById = generateTreeById($items);

$scanTreeLists = scanTreeData($treeById);

var_dump($scanTreeLists);

exit();
