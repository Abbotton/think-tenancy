# 基于ThinkPHP6开发的多租户扩展

[![Latest Version on Packagist](https://img.shields.io/packagist/v/abbotton/think-tenancy.svg?style=flat-square)](https://packagist.org/packages/abbotton/think-tenancy)
[![Total Downloads](https://img.shields.io/packagist/dt/abbotton/think-tenancy.svg?style=flat-square)](https://packagist.org/packages/abbotton/think-tenancy)
![GitHub Actions](https://github.com/abbotton/think-tenancy/actions/workflows/main.yml/badge.svg)

`无侵入`、`简单易上手`、`高度可自定义`的`ThinkPHP v6.x` 多租户扩展。

### 功能特性
- [x] 通过`子域名`识别租户，实现租户`数据库`、`文件存储`、`缓存`、`Session`、`Cookie`、`视图`的隔离；
- [x] 可自定义`租户`租户模型以及字段名称；
- [x] 完善的`事件`机制，通过监听或者订阅事件，可实现更多自定义的需求；
- [x] 扩展`数据迁移`和`数据填充`指令，可以方便的维护租户的`数据迁移`和`数据填充`；
- [ ] 高覆盖率的单元测试（WIP）；

### 安装

准备工作：

- 创建`ThinkPHP v6.x`项目；
- 完成泛域名解析；

开始安装：
```bash
# 引入扩展
composer require abbotton/think-tenancy
# 发布数据迁移文件，发布后的文件位于`database/migrations`文件夹中
php think tenants:publish
```

### 使用

#### 单数据库模式：

- 修改`config/tenancy.php`以及`database/migrations/20220422082201_add_tenants_table.php`中的相关配置；
- 执行数据迁移，创建租户信息表：`php think migrate:run`；
- 自主完善租户信息的创建逻辑；
- 监听`think\tenancy\events\TenantCreated::class`事件进行自定义操作：
  - 创建租户`OSS Bucket`；
  - 向租户发送邮件通知；
  - ......
- 访问租户对应域名验证租户信息是否正确；

#### 多数据库模式：
- 修改`config/tenancy.php`以及`database/migrations/20220422082201_add_tenants_table.php`中的相关配置；
- 执行数据迁移，创建租户信息表：`php think migrate:run`；
- 自主完善租户信息的创建逻辑；
- 通过`php think tenants:migrate:create`命令创建租户的数据迁移文件；
- 通过`php think tenants:seed:create`命令创建租户的数据填充文件；
- 监听`think\tenancy\events\TenantCreated::class`事件进行自定义操作：
   - 创建租户数据库；
  - 创建租户数据库用户并授权；
  - 通过`php think tenants:migrate:run --tenant=sub_domain`执行租户数据迁移；
  - 通过`php think tenants:seed:run --tenant=sub_domain`执行租户数据填充；
  - 创建租户`OSS Bucket`；
  - 向租户发送邮件通知；
  - ......
- 访问租户对应域名验证租户信息是否正确；

### 命令行

#### 基本说明

- 租户的`数据迁移`文件默认存放在`database/migrations/tenants`文件夹中，可修改`config/tenancy.php`自定义，也可以通过`path`参数指定；
- 租户的`数据填充`文件默认存放在`database/seeds/tenants`文件夹中，可修改`config/tenancy.php`自定义，也可以通过`path`参数指定；
- `path`参数传参需传入绝对路径；
- `--tenant`选项需要传入租户的**二级域名标识**，否则会因为匹配不到租户而报错；

#### 命令列表

```bash
# 创建租户迁移文件
tenants:migrate:create <name> [<path>]

# 执行租户数据迁移
tenants:migrate:run [options] [--] [<path>]

# 执行租户数据迁移回滚
tenants:migrate:rollback [options] [--] [<path>]

# 查看租户数据迁移状态
tenants:migrate:rollback [options] [--] [<path>]

# 创建租户数据填充文件
tenants:seed:create <name> [<path>]

# 执行租户数据填充
tenants:seed:run [options] [--] [<path>]

# 发布租户表数据迁移文件
tenants:publish
```

### 测试(WIP)

```bash
composer test
```

### 更新日志

请查看 [CHANGELOG](CHANGELOG.md) 获取更多信息.

### 致谢

- [Abbotton](https://github.com/Abbotton)
- [ThinkPHP](https://github.com/top-think/think)
- [Tenancy for Laravel](https://github.com/archtechx/tenancy)

### License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.