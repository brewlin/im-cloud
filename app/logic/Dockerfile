# brew/logic
#
# @link     https://www.github.com/brewlin/im-cloud
# @license  https://github.com/brewlin/im-cloud/blob/master/LICENSE

FROM brewlin/im-cloud-base

ADD . /website/im-cloud

EXPOSE 9600

ENTRYPOINT ["php", "/website/im-cloud/app/logic/bin/app","--start","--log=true","--debug"]
