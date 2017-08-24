<?php
namespace risk;

class Config
{
    public function getConfig()
    {
        $testing = array(
            // 联调接口
            'SERVER_URL'    => 'https://openapitest.cafintech.com:8088/authen',
            // 图片上传地址
            'UPLOAD_URL'    => 'https://openapitest.cafintech.com:8088/file/upload',
            // 客户编码,由诚安提供
            'PARTNER_ID'    => '78fb6q93d14cua0bbsa9aa49ebe601y3',
            // 客户私钥,在SAAS系统中申请
            'PRIVATE_KEY'   => 'MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAJSRxySDvAX74EkVtv6ehUSwvEQVp03EF80PFfBNXzTm5S97keTJygwmo2wzcO0PpusCPFanhxqVnNPhmUDVJazV5S4mb5ayamjsMdx+q/a5f3K2ecGEEG5PTv2nzSF8GLwUZCsNwEOIwWHJvHjiDUm8xPgJPntH1x5yVLDmTtabAgMBAAECgYAgDph2s4oVjHSnsGzM4e2FldD5q+ZurDoqf+/O6xL4+j1HkpU3VacoGgo3JZ5fOHpeyRu14u4O+WteeJY13AgFv1id0uFq4BGnduZtV96WlzXk3bntW9oahgZE6U/OUii6Z3TPOceLEpWnkfUT1s9JikYnveMWptUgl+LYdry7yQJBANvlkbLM7JIGYe5nfyBs382wCQ9UytI9QT+Sm4l1B2h/jjK7QrksBRE8MIUpjtA7vjCZbk/pJm30Agm10QUSJJcCQQCs9kRHehTIarKjPUM0ERk99GDhMAX1tEjrllAhoEAhaQzbiXe8GTKt48MB1URVy2I1FIc+NxttuTGkyc8204qdAkBkBLJVonIEJVUL7BVduUe+tdAZIhcys1vnP5bxWcKp3ELgfl5l/Ui8wyTKnNFxk4r5bgBH5qNtJax7oDBXXx43AkEAgDUPybfJngHRJsVrgjXGczEpeuKBGG8puk+yWCqUPu/Ckx0j/u7irRjuXPZ77+iRhG0SDuEEWOH01YSuL6LA1QJBAIB7G8xyVMBBFIt6kDPvCDxvTWmkXRflavRbk+Sq2Fi/TjC5gyJg6U733I+luBNaSEZpDifWxX9dQPbNNDJUXGo=',
            // 诚安聚立服务公钥,由诚安提供
            'PUBLIC_KEY' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCevu5fpIaVr67xFL86AU0hVCIRnreWuBBi6AS6/11YauWa8DThX48k6L6kzrdQKhVARoVWAm8jtYrCQ9izgBUyzJ4xtOuFSQsSVO9bVxDBxfpx8u/G6kQwjU+/cNGZFLvmLjZvfZ+8O3EpGjpsGsZ3/yQiDIn9spZeMFR+b/iDDQIDAQAB',
            // 服务器证书路径，证书由诚安提供
            'CERT_PATH'  => __DIR__ . '/key/ca_uat.crt',
            'CONNECT_TIMEOUT'   => '30000',
            'READ_TIMEOUT'      => '45000'
        );
        $production = array(
            // 联调接口
            'SERVER_URL'    => 'https://openapi.cafintech.com/openapi/authen',
            // 图片上传地址
            'UPLOAD_URL'    => 'https://openapi.cafintech.com/file/upload',
            // 客户编码,由诚安提供
            'PARTNER_ID'    => 'b4f0130bad314676b4552705a683c711',
            // 客户私钥,在SAAS系统中申请
            'PRIVATE_KEY'   => 'MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAJXXopspvO3ukk64FwxS82XozphVFeDkp5Hm/QJ4ne/dcaLQtSfdZ3N5AOiMeVnHgZqMQYTgeVaMg78FJtU9CxlCZWmNhfBdu5jBUkbKd0RrW07U+BgZWojMkbJu7fDb7FGMnlzfHkKzZNyg0eMqa5Gr65US9Bmaq3uVy5yWcXpXAgMBAAECgYEAin/mLMVHfCUn1tsRcrK5h1jInMUIk/bFbHa1JbOXSD7G6lc/GrnrBTCzRj+RawqdINzDMq7JeNYocEeyvSbPOf+pKZoInGtErMqKNx0ezWMirsjCHRAQjyrYIKUICSso6+wv9QX6M0v1bAtZOvq4e/UGGUCdCeaT521RQbfd6AECQQDG4fpuvYKGqpn3/RyE60F+87lOUYQPNsChfjo3rjs5osTYSJFzH3bMtA4dyGrnTpJM/u6I/p+BQzGppQP9ZBNhAkEAwOAqiuTeaCtDsSniBm9ME9NKbBK94rqSf9ZuFc8iSCTwSJH4F4tVi17AtvHpgT05DyCFPiIPYixSCApEtqugtwJATQRHucizmjjIpSskdyivVV1Gnlic3lNip2c9XkCfeMNanTME+GLv23fCm6/4Dhj0ONLkRrLry9/chIh9kgvGIQJAHAs8BE/8ypan1AWr+JWMMUHCi246L9JC5NWP0hn4+RZt3Y9jzECVIpuXV8Ja2lDFkB6BYSRgqjyZfNPJY+oWhwJBAL4UOOpL1ZU4Ute02zmftFKumxSEee3DXYmwg+trT8wNeQ4vW0uCCLgZN3BxUfCy/avECdIjYJ+re247i9b9b80=',
            // 诚安聚立服务公钥,由诚安提供
            'PUBLIC_KEY' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCevu5fpIaVr67xFL86AU0hVCIRnreWuBBi6AS6/11YauWa8DThX48k6L6kzrdQKhVARoVWAm8jtYrCQ9izgBUyzJ4xtOuFSQsSVO9bVxDBxfpx8u/G6kQwjU+/cNGZFLvmLjZvfZ+8O3EpGjpsGsZ3/yQiDIn9spZeMFR+b/iDDQIDAQAB',
            // 服务器证书路径，证书由诚安提供
            'CERT_PATH'  => __DIR__ . '/key/ca_online.crt',
            'CONNECT_TIMEOUT'   => '30000',
            'READ_TIMEOUT'      => '45000'
        );
        return $production;
    }
}