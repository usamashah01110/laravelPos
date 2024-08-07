<?php
//
//namespace Tests\Feature;
//
//use Illuminate\Foundation\Testing\RefreshDatabase;
//use Illuminate\Foundation\Testing\WithFaker;
//use Tests\TestCase;
//
//
//class OtpSmsTest extends TestCase
//{
//    use RefreshDatabase;
//    use WithFaker;
//
//    public function testSuccesfulOtpSms()
//    {
//
//        $response = $this->post('/api/v1/send-otp', [
//            'phone' => '923158192177',
//        ]);
//        $response->assertValid(['phone']);
//        $response->assertStatus(200);
//        $response->assertJsonStructure(['message','otp']);
//    }
//
//    public function testUnSuccesfulOtpSms()
//    {
//
//        $response = $this->post('/api/v1/send-otp', [
//            'phone' => '923158192170',
//        ]);
//        $response->assertValid(['phone']);
//        $response->assertStatus(200);
//        $response->assertJsonStructure(['success','messege']);
//    }
//}
