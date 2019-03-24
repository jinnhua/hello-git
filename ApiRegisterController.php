<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/24
 * Time: 15:36
 */

namespace App\Http\Controllers\Api;

use Com\BKSY\Business\Model\User;
use Com\BKSY\Business\Simple\Helper\Vcs\Main\AuthVerifyCode;
use Com\BKSY\Business\Constant\CacheKeyPrefix;
use Com\BKSY\Common\Util\StringUtil;
use Com\BKSY\Business\Response\Result;
use Com\BKSY\Business\Enum\Code\UserEnum;

use App\Http\Controllers\Auth\HomeRegisterController;
use Illuminate\Http\Request;


class ApiRegisterController extends ApiBaseController
{
    private $verifyCodeService;

    public function cacheKey($mobile)
    {
        return CacheKeyPrefix::USER_LOGIN_VERIFY . '_' . $mobile;
    }

    public function register(Request $request)
    {

        var_dump(123);
        $result = new Result();
        $mobile = $request->get('mobile');
        $cache_key = $this->cacheKey($mobile);
        ##验证手机号码是否正确
        if (!StringUtil::isMobile($mobile)) {
            $result->failed(UserEnum::MOBILE_ERROR());
            return response($result);
        }
        if (env('APP_ENV') === 'production') {//DEBUG 暂时不校验验证码
            $verify_code = $request->get('verify_code');
            if ($verify_code) {

                $cache_value = \Cache::get($cache_key);

                if (empty($cache_value['verify_code'])) {
                    $result->failed(UserEnum::VERIFY_CODE_INVALID());
                    return response($result);
                }

                if ($verify_code != $cache_value['verify_code']) {
                    $result->failed(UserEnum::VERIFY_CODE_ERROR());
                    return response($result);
                }

            }
        }

    }

    /*
     * 发送短信验证
     */
    public function send(AuthVerifyCode $verifyCode): Result
    {
        var_dump(123);
        $result = new Result();
        $this->verifyCodeService->send($verifyCode);
        return $result->succeed();
    }
   



}