# Simple Instant Message

An online instant messaging program. You can run this on your server and use it to talk to your friends!

A demo is available at http://musiclifephilosophy.com/codes/message/

## Installation

You need a server that runs php and git and preferably has inotify enabled. Then cd to any directory and do

```
$ git clone https://github.com/xinxinw1/message.git
```

Then visit `http://<your site>/<some directory>/message/`

## Basic Usage

Go to `http://<your site>/<some directory>/message/` and follow the instructions.

You can change the current name or the current doc by clicking on the name or doc at the top of the page.

When there is an unread message, the page title has `(!)` added to the front.

## Advanced Usage

To go to a page directly, go to `http://<your site>/<some directory>/message/?name=<your name>&doc=<document name>`

All documents are stored under `docs/`. Document names (but not contents) are encoded with a [URL-safe version of base64](http://stackoverflow.com/questions/11449577/why-is-base64-encode-adding-a-slash-in-the-result).

## License

This program is dedicated to the public domain using the [Creative Commons CC0](http://creativecommons.org/publicdomain/zero/1.0/). See `LICENSE.txt` for details.

This previous does not apply to `webtoolkit.base64.js`. The license for that file is at http://www.webtoolkit.info/license1/index.html (They use CC-BY or Creative Commons Attribution)
