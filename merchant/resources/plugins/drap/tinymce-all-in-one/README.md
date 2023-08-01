[![](https://data.jsdelivr.com/v1/package/npm/tinymce-all-in-one/badge)](https://www.jsdelivr.com/package/npm/tinymce-all-in-one)

# Why

Now tinymce's cdn, all plugins are downloaded separately, but there are dozens of plugins. So the init is very slow.
Therefore, this project will build all plugins into `tinymce.min.js`
through [Offical Build](https://www.tiny.cloud/get-tiny/custom-builds/), speeding up the init speed. Didn't do anything
else.

And

Currently Tinymce's lang does not have cdn. So create an npm package, let lang supports cdn.

# Use

Cnd in [jsdelivr](https://www.jsdelivr.com/package/npm/tinymce-all-in-one)

**If you don't mind the init speed, it is recommended to use the
official [cdn](https://www.jsdelivr.com/package/npm/tinymce).**

## Langs

Cnd in [jsdelivr](https://www.jsdelivr.com/package/npm/tinymce-lang?path=langs)

- [zh_CN](https://cdn.jsdelivr.net/npm/tinymce-lang/langs/zh_CN.js)
- [ja](https://cdn.jsdelivr.net/npm/tinymce-lang/langs/ja.js)
- ...

[support langs](https://www.tiny.cloud/get-tiny/language-packages/)

```js
tinymce.init({
  selector: "textarea", // change this value according to your HTML
  language: "zh_CN", // select language
  language_url: "https://cdn.jsdelivr.net/npm/tinymce-lang/langs/zh_CN.js" // site absolute URL
});
```
