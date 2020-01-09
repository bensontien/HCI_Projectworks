package com.example.bmiandbmr;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.*;

public class MainActivity extends AppCompatActivity {

    public static final String EXTRA_NAME= "com.example.bmiandbmr.example.EXTRA_NAME";
    public static final String EXTRA_AGE= "com.example.bmiandbmr.example.EXTRA_AGE";
    public static final String EXTRA_GENDER= "com.example.bmiandbmr.example.EXTRA_GENDER";
    public static final String EXTRA_WEIGHT= "com.example.bmiandbmr.example.EXTRA_WEIGHT";
    public static final String EXTRA_HIGHT= "com.example.bmiandbmr.example.EXTRA_HIGHT";
    public static final String EXTRA_BMI= "com.example.bmiandbmr.example.EXTRA_BMI";
    public static final String EXTRA_BMR= "com.example.bmiandbmr.example.EXTRA_BMR";


    TextView testName, testGender;
    TextView testAge, testHight, testWeight;
    String TestName;
    String TestGender;
    int StringEqualFemale = 1;
    int StringEqualMale = 1;

    Button Btn_caculate;

    double BMI, BMR;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        InitialXML();
    }

    void InitialXML(){

      testName = findViewById(R.id.nameID) ;
      testGender = findViewById(R.id.genderID);
      testAge = findViewById(R.id.ageID);
      testHight = findViewById(R.id.heightID);
      testWeight = findViewById(R.id.weightID);

      Btn_caculate = findViewById(R.id.btn_Caculate);
      Btn_caculate.setOnClickListener(BtncaculateOnClick);

    }

    Button.OnClickListener BtncaculateOnClick = new View.OnClickListener() {
        @Override
        public void onClick(View v) {

            Log.i("Click","Click");
            Cal();
            openActivity2();
        }
    };

    public void Cal(){

        TestName = testName.getText().toString();
        TestGender = testGender.getText().toString();

        double TestAge = Double.parseDouble(testAge.getText().toString());
        double TestHight = Double.parseDouble(testHight.getText().toString());
        double TestWeight = Double.parseDouble(testWeight.getText().toString());

        BMI = TestWeight/Math.pow(TestHight/100,2);

        StringEqualFemale = TestGender.compareTo("Female");
        StringEqualMale = TestGender.compareTo("Male");

        if(StringEqualFemale==0){

            BMR =  655 + (9.6 * TestWeight)+(1.8 *TestHight)-(4.7*TestAge);

        }

        if(StringEqualMale==0){

            BMR =  66 + (13.7 * TestWeight)+(5.0 *TestHight)-(6.8*TestAge);

        }

    }

    public void openActivity2(){

        Intent intent = new Intent(this, Activity2.class);
        intent.putExtra(EXTRA_NAME,TestName);
        intent.putExtra(EXTRA_BMI,BMI);
        intent.putExtra(EXTRA_BMR,BMR);
        startActivity(intent);

    }


}
