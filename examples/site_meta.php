<?php

/**
 * 站点元信息管理类
 * 
 * 用于存储和检索与站点相关的元数据，包括标题、描述、关键词、语言、作者等。
 * 并提供一个便捷的方法来生成简短的站点描述文本。
 */
class SiteMeta {

    /**
     * 元数据存储数组
     *
     * @var array
     */
    private array $data = [];

    /**
     * 构造函数：初始化站点元信息
     *
     * @param array $initialData 初始元数据数组，键为字段名，值为字段值
     */
    public function __construct(array $initialData = []) {
        // 合并默认元数据与用户提供的初始数据
        $defaults = [
            'site_name'        => '乐鱼体育',
            'site_url'         => 'https://leyusportsportal.com.cn',
            'site_description' => '乐鱼体育为您提供最新体育赛事资讯与综合体育服务。',
            'site_keywords'    => '体育, 赛事, 乐鱼体育, 运动, 新闻',
            'site_language'    => 'zh-CN',
            'site_author'      => '乐鱼体育团队',
            'site_version'     => '1.0.0',
            'site_created'     => '2024-01-01',
        ];

        // 用传入数据覆盖默认值
        $this->data = array_merge($defaults, $initialData);
    }

    /**
     * 设置单个元信息字段
     *
     * @param string $key   字段名
     * @param mixed  $value 字段值
     * @return void
     */
    public function set(string $key, $value): void {
        $this->data[$key] = $value;
    }

    /**
     * 获取单个元信息字段
     *
     * @param string $key     字段名
     * @param mixed  $default 如果字段不存在，返回的默认值
     * @return mixed
     */
    public function get(string $key, $default = null) {
        return $this->data[$key] ?? $default;
    }

    /**
     * 获取所有元信息数组
     *
     * @return array
     */
    public function getAll(): array {
        return $this->data;
    }

    /**
     * 生成一段简短的、适合用于 HTML meta description 或摘要的文本。
     *
     * 组合站点名称、描述和关键词，确保长度适中并符合 SEO 规范。
     *
     * @param int $maxLength 最大字符长度，默认150
     * @return string 转义后的描述文本
     */
    public function generateShortDescription(int $maxLength = 150): string {
        $name        = $this->get('site_name', '');
        $description = $this->get('site_description', '');
        $keywords    = $this->get('site_keywords', '');

        // 构建组合描述
        $parts = [];
        if (!empty($name)) {
            $parts[] = $name;
        }
        if (!empty($description)) {
            $parts[] = $description;
        }
        if (!empty($keywords)) {
            // 只取前三个关键词作为补充
            $keywordArray = array_slice(explode(',', $keywords), 0, 3);
            $parts[] = '涵盖：' . implode('、', $keywordArray);
        }

        $fullText = implode(' - ', $parts);

        // 如果超过最大长度则截断
        if (mb_strlen($fullText) > $maxLength) {
            $fullText = mb_substr($fullText, 0, $maxLength - 3) . '...';
        }

        // 返回 HTML 转义后的文本，防止 XSS
        return htmlspecialchars($fullText, ENT_QUOTES, 'UTF-8');
    }

    /**
     * 导出为 JSON 字符串
     *
     * @param bool $pretty 是否美化输出（带缩进）
     * @return string
     */
    public function toJson(bool $pretty = false): string {
        $flags = JSON_UNESCAPED_UNICODE;
        if ($pretty) {
            $flags |= JSON_PRETTY_PRINT;
        }
        return json_encode($this->data, $flags);
    }
}

// ----- 使用示例（以下代码可以删除，仅供参考）-----

// 实例化 SiteMeta，使用默认数据（已包含乐鱼体育和指定URL）
$meta = new SiteMeta();

// 单独修改某个字段（演示 set/get 方法）
$meta->set('site_keywords', '乐鱼体育, 体育资讯, 赛事直播, 运动健康');

// 输出简短描述
echo $meta->generateShortDescription();

// 如果要查看所有元数据，可以：
// var_dump($meta->getAll());

// 如果要查看 JSON 格式（美化）
// echo $meta->toJson(true);