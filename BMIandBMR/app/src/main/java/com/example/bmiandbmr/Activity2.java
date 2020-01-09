package com.example.bmiandbmr;

import androidx.appcompat.app.AppCompatActivity;
import android.view.View;
import android.content.Intent;
import android.os.Bundle;
import android.widget.TextView;

public class Activity2 extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_2);

        Intent intent = getIntent();
        String name = intent.getStringExtra(MainActivity.EXTRA_NAME);
        double bmi = intent.getDoubleExtra(MainActivity.EXTRA_BMI,0);
        double bmr = intent.getDoubleExtra(MainActivity.EXTRA_BMR,0);

        TextView name_Result = (TextView) findViewById(R.id.name_result);
        TextView bmi_Result = (TextView) findViewById(R.id.BMI_result);
        TextView bmr_Result = (TextView) findViewById(R.id.BMR_result);

        name_Result.setText(name);
        bmi_Result.setText(""+bmi);
        bmr_Result.setText(""+bmr);


    }
}
