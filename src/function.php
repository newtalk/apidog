<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
if (! function_exists('array_map_recursive')) {
    function array_map_recursive(callable $func, array $data)
    {
        $result = [];
        foreach ($data as $key => $val) {
            $result[$key] = is_array($val) ? array_map_recursive($func, $val) : call($func, [$val]);
        }

        return $result;
    }
}

if (! function_exists('array_sort_by_value_length')) {
    function array_sort_by_value_length($arr, $sort_order = SORT_DESC)
    {
        $keys = array_map('strlen', $arr);
        array_multisort($keys, $sort_order, $arr);
        return $arr;
    }
}

if (! function_exists('array_sort_by_key_length')) {
    function array_sort_by_key_length($arr, $sort_order = SORT_DESC)
    {
        $keys = array_map('strlen', array_keys($arr));
        array_multisort($keys, $sort_order, $arr);
        return $arr;
    }
}

if (! function_exists('array_sort_by_key')) {
	function array_sort_by_key($list, $key, $sort_order=SORT_ASC) {
		if(is_array($list)){
			$refer = $resultSet = array();
			foreach ($list as $i => $data)
				$refer[$i] = &$data[$key];

			switch ($sort_order) {
				case SORT_ASC: // 正向排序
					asort($refer);
					break;
				case SORT_DESC:// 逆向排序
					arsort($refer);
					break;
				case SORT_NATURAL: // 自然排序
					natcasesort($refer);
					break;
			}
			foreach ( $refer as $i=> $val)
				$resultSet[] = &$list[$i];
			return $resultSet;
		}
		return false;
	}
}
