## 实时抓取中国上市互联网公司市值和其排名

#### 安装
```bash
composer create-project zhyee/rrclic rrclic

#如果安装速度过慢，请替换阿里云composer镜像
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
```
  或者
```bash
  git clone https://github.com/zhyee/Ranking-of-Chinese-Internet-Corp.git rrclic
  cd rrclic && composer install
```

#### 使用

```bash
#进入项目目录
cd rrclic

php public/index.php
```

会输出类似如下信息：
```
01. 腾讯控股            9112.08亿美元           58799.24亿元            70663.67亿港元
02. 阿里巴巴            7188.06亿美元           46395.36亿元            55720.36亿港元
03. 美团                3085.27亿美元           19908.95亿元            23926.15亿港元
04. 拼多多              2405.56亿美元           15526.69亿元            18647.40亿港元
05. 快手                1624.97亿美元           10485.76亿元            12601.56亿港元
06. 京东                1502.88亿美元           9700.39亿元             11650.07亿港元
07. 百度                921.34亿美元            5946.83亿元             7142.09亿港元
08. 小米集团            882.13亿美元            5692.32亿元             6840.91亿港元
09. 网易                857.00亿美元            5531.55亿元             6643.34亿港元
10. 贝壳                738.15亿美元            4764.39亿元             5721.99亿港元
11. 京东健康            639.34亿美元            4125.60亿元             4958.05亿港元
12. 哔哩哔哩            482.86亿美元            3116.65亿元             3743.07亿港元
13. 好未来              482.48亿美元            3114.18亿元             3740.10亿港元
14. 东方财富            478.16亿美元            3086.94亿元             3706.18亿港元
15. 阿里健康            458.06亿美元            2955.85亿元             3552.28亿港元
16. 腾讯音乐            430.28亿美元            2777.28亿元             3335.49亿港元
17. 陆金所              423.01亿美元            2730.37亿元             3279.14亿港元
18. 金山办公            291.34亿美元            1880.88亿元             2258.18亿港元
19. 唯品会              224.19亿美元            1447.06亿元             1737.90亿港元
20. 跟谁学              214.04亿美元            1381.57亿元             1659.25亿港元
21. 携程                207.09亿美元            1336.72亿元             1605.39亿港元
22. 用友网              200.43亿美元            1293.94亿元             1553.50亿港元
23. 深信服              198.00亿美元            1278.27亿元             1534.70亿港元
24. 爱奇艺              181.84亿美元            1173.72亿元             1409.63亿港元
25. 三六零              174.76亿美元            1128.25亿元             1354.58亿港元
26. 恒生电子            165.36亿美元            1067.58亿元             1281.73亿港元
27. 富途证券            156.98亿美元            1013.23亿元             1216.88亿港元
28. 金蝶国际            156.05亿美元            1006.99亿元             1210.18亿港元
29. 汽车之家            155.06亿美元            1000.85亿元             1202.01亿港元
30. 科大讯飞            152.52亿美元            984.66亿元              1182.19亿港元
31. 平安好医生          152.38亿美元            983.30亿元              1181.71亿港元
32. 金山云              139.42亿美元            899.90亿元              1080.77亿港元
33. 同花顺              130.65亿美元            843.49亿元              1012.69亿港元
34. 金山软件            120.90亿美元            780.15亿元              937.57亿港元
35. 微博                115.57亿美元            745.98亿元              895.92亿港元
36. 达达                108.48亿美元            700.23亿元              840.96亿港元
37. 阅文集团            100.85亿美元            650.82亿元              782.15亿港元
38. 声网                98.23亿美元             634.03亿元              761.47亿港元
39. 苏宁易购            94.45亿美元             609.80亿元              732.13亿港元
40. 欢聚时代            92.95亿美元             599.96亿元              720.55亿港元
41. 微盟集团            69.83亿美元             450.65亿元              541.59亿港元
42. 完美世界            68.99亿美元             445.41亿元              534.76亿港元
43. 浪潮信息            68.72亿美元             443.67亿元              532.67亿港元
44. 世纪华通            67.76亿美元             437.46亿元              525.22亿港元
45. 虎牙                62.98亿美元             406.56亿元              488.27亿港元
46. 巨人网络            50.64亿美元             326.93亿元              392.52亿港元
47. 前程无忧            46.51亿美元             300.21亿元              360.55亿港元
48. 斗鱼                46.00亿美元             296.92亿元              356.60亿港元
49. 同程艺龙            41.63亿美元             268.68亿元              322.89亿港元
50. 网易有道            40.94亿美元             264.25亿元              317.36亿港元
51. 昆仑万维            37.94亿美元             244.99亿元              294.14亿港元
52. 老虎证券            36.86亿美元             237.91亿元              285.73亿港元
53. 陌陌                36.15亿美元             233.35亿元              280.25亿港元
54. 万达信息            35.54亿美元             229.44亿元              275.46亿港元
55. 拉卡拉              33.89亿美元             218.80亿元              262.69亿港元
56. 搜狗                32.20亿美元             207.89亿元              249.67亿港元
57. 泛微网络            31.17亿美元             201.28亿元              241.66亿港元
58. 360数科             30.28亿美元             195.47亿元              234.76亿港元
59. 人民网              29.30亿美元             189.18亿元              227.13亿港元
60. 新浪                25.63亿美元             165.43亿元              198.68亿港元
61. 大智慧              24.81亿美元             160.20亿元              192.34亿港元
62. 东软集团            17.43亿美元             112.55亿元              135.13亿港元
63. 美图公司            15.58亿美元             100.54亿元              120.83亿港元
64. 顺网科技            15.44亿美元             99.69亿元               119.69亿港元
65. 二三四五            14.89亿美元             96.17亿元               115.47亿港元
66. 游族网络            14.25亿美元             92.04亿元               110.50亿港元
67. 恺英网络            14.23亿美元             91.91亿元               110.35亿港元
68. 新华网              13.73亿美元             88.65亿元               106.43亿港元
69. 二六三              11.75亿美元             75.85亿元               91.07亿港元
70. 信也科技            11.22亿美元             72.45亿元               87.01亿港元
71. 趣头条              11.15亿美元             72.01亿元               86.49亿港元
72. 趣店                7.61亿美元              49.13亿元               59.00亿港元
73. 搜狐                7.35亿美元              47.44亿元               56.98亿港元
74. 博彦科技            7.09亿美元              45.82亿元               55.01亿港元
75. 启明信息            7.02亿美元              45.34亿元               54.44亿港元
76. 浪潮国际            6.24亿美元              40.27亿元               48.40亿港元
77. 浪潮软件            6.23亿美元              40.25亿元               48.32亿港元
78. 映客                6.15亿美元              39.74亿元               47.76亿港元
79. 科大国创            5.82亿美元              37.60亿元               45.14亿港元
80. 极光                5.76亿美元              37.19亿元               44.67亿港元
81. 天源迪科            5.69亿美元              36.79亿元               44.17亿港元
82. 网达软件            5.10亿美元              32.98亿元               39.60亿港元
83. 猎豹移动            4.93亿美元              31.85亿元               38.25亿港元
84. 乐居                4.44亿美元              28.68亿元               34.44亿港元
85. 宜人贷              4.09亿美元              26.41亿元               31.72亿港元
86. 迅雷                3.67亿美元              23.69亿元               28.46亿港元
87. 蘑菇街              3.35亿美元              21.63亿元               25.98亿港元
88. 途牛                3.30亿美元              21.34亿元               25.62亿港元
89. 齐屹科技            3.25亿美元              21.01亿元               25.25亿港元
90. 触宝                2.13亿美元              13.75亿元               16.51亿港元
91. 第九城市            2.05亿美元              13.28亿元               15.95亿港元
92. 流利说              2.02亿美元              13.04亿元               15.66亿港元
93. 人人网              1.82亿美元              11.77亿元               14.14亿港元
94. 鲁大师              1.42亿美元              9.22亿元                11.08亿港元
95. 房天下              1.20亿美元              7.80亿元                9.36亿港元
96. 国双                0.64亿美元              4.15亿元                4.99亿港元
```

May you enjoy it!
