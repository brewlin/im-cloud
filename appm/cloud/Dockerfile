# brew/cloud
#
# @link     https://www.github.com/brewlin/im-cloud
# @license  https://github.com/brewlin/im-cloud/blob/master/LICENSE

FROM brewlin/im-cloud-base

ADD . /website/im-cloud

EXPOSE 9500 9501

ENTRYPOINT ["php", "/website/im-cloud/app/cloud/bin/app","--start","--debug","--log=true"]
