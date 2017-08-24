<?php
namespace risk;

class SysParam
{
    protected $OPEN_API_URL = "";//公共api的url

    public static $CREDIT_APPLY_SYN = "psaicesubmit";

    public static $CREDIT_APPLY_ASYN = "psaiceAysSubmit";

    public static $CREDIT_SELECT = "psaicequery"; // 申请结果查询接口

    public static $PAY_DAY_LOAN_CREDIT_APPLY_SYN = "ca1101PsaiceSubmit"; // 信审申请接口（同步）

    public static $PAY_DAY_LOAN_CREDIT_APPLY_ASYN = "ca1101PsaiceAysSubmit";// 信审申请接口（异步）

    //我的公钥
    protected $publicKey = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCusSqH8FtPulj92lx7wQidnTQjfIo5ydcu5UmPftljyjk9d+nQ9ed4L4SAtVQrm0sIjUIXadfW+POeRg1b9WfQJqJf21rfLomIiMQHbJCR6qvONZL+Ng8c7pRjAO8y8x53ZKB4ntfw4yJFNOkNPTCSAUXDaIxZlfd66z1CVmUZFQIDAQAB";
    //我的私钥
    protected $selfSecretKey =null;//"MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAK6xKofwW0+6WP3aXHvBCJ2dNCN8ijnJ1y7lSY9+2WPKOT136dD153gvhIC1VCubSwiNQhdp19b4855GDVv1Z9Amol/bWt8uiYiIxAdskJHqq841kv42DxzulGMA7zLzHndkoHie1/DjIkU06Q09MJIBRcNojFmV93rrPUJWZRkVAgMBAAECgYEAgfYj4gYaqHHGCvUMoLS3KCrvwwa//sX+ZFEILMz+BZNIikZPmLmYfn07YlWETPy3EaGlba28eZ20ACe1gJhNpfrGnPARPYZdA2ge6rSmGR5F7FCiG/uONVQL6khazYmyhB2edEQq7lJP/5svR/ovhVioXcId1DjsfOHhSPHOTAECQQD3ju95a0djVflN/+FunlmIQElhl1WZf9zgfABTWoA44RZcwZUEf53V4611dhfED2AztlX2l63EkSBCJeLyeg1VAkEAtKYkrOHu1JOCxONCY8Mk7yReocuUoSBqR4J6Y3UJkgUFCfUZkNBnZYxKxx7unl6r48r/wb8fly1SoAcfcTzcwQJAILskTIBzot2mJbr0OmTzX4Focl/I8I+oS4H5pQutMlgIVeE+a6bX3oTI1WP0xnZl+NBd00nAruGlSzmpJPggNQJBAI+wy+FcAR3Di7PSVL+HvHwfwMoPZdTYNNFWnsU3lfo41e46sDA5JNVoRx6lowYDpdQWZ2MUBCu62EsD/2WSFMECQHpn7JrPa3P4Liyi1ZqRi6ot1EVBLLrdlUd2DOVcdPSIa3mcr3WVwqhmRKngGBMp4yrJ5EuE1/Zw/vPPWdtSbos=";
    //诚安聚立公钥
    protected $caPublicKey =null;// "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCevu5fpIaVr67xFL86AU0hVCIRnreWuBBi6AS6/11YauWa8DThX48k6L6kzrdQKhVARoVWAm8jtYrCQ9izgBUyzJ4xtOuFSQsSVO9bVxDBxfpx8u/G6kQwjU+/cNGZFLvmLjZvfZ+8O3EpGjpsGsZ3/yQiDIn9spZeMFR+b/iDDQIDAQAB";

    //版本
    protected $version = "v1";
    //我的唯一token
    protected $idCoop = "";

    protected $keyStore ;

    protected $keyStoreFilePassWord;

}